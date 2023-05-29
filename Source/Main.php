<?php
/**
 * @package wp-rewatermark
 * @author Bogdanov Andrey (swarzone2100@yandex.ru)
 */
namespace wpr;
use Intervention\Image\ImageManager;
use WP_REST_Request;

class Main
{
    public function __construct()
    {
        $this->apiInit();
        $this->getShortcodeForm();
    }

    private function getShortcodeForm()
    {
        add_shortcode('wpr-form', function($atts, $content)
        {
            $user_info_per = get_userdata( get_current_user_id() );

            if ($user_info_per->user_level <= 7)
                return 'У вас нет прав просмотра этой страницы!';

            wp_enqueue_script( 'wpr-form-datamanager', plugin_dir_url( __FILE__ ) . 'Assets/Classes/DataManager.js', [], '1.0', true );
            wp_enqueue_script( 'wpr-form-progressbar', plugin_dir_url( __FILE__ ) . 'Assets/Classes/ProgressBar.js', [], '1.0', true );
            wp_enqueue_script( 'wpr-form-script', plugin_dir_url( __FILE__ ) . 'Assets/Script.js', [], '1.0', true );
            wp_enqueue_style( 'wpr-form-style', plugin_dir_url( __FILE__ ) . 'Assets/Style.css', [], '1.0' );
            ob_start();
            include __DIR__ . '/Templates/Form.php';
            return ob_get_clean();
        });
    }

    private function getAnswerApi( $code, $message, $content )
    {
        return [
            'code' => $code,
            'message' => $message,
            'content' => $content
        ];
    }

    private function apiInit()
    {
        add_action('rest_api_init', function()
        {
            register_rest_route(
                'wpr/v1',
                '/getlists',
                [
                    'methods' => 'POST',
                    'callback' => function( WP_REST_Request $request )
                    {
                        $posts = get_posts( [
                          'post_type' => 'ulz_listing',
                          'posts_per_page' => -1,
                          'fields' => 'ids',
                        ] );

                        if ( !$posts )
                            return $this->getAnswerApi( 99, 'Список пуст.', [] );

                        return $this->getAnswerApi( 0, 'Список получен.', $posts );
                    }
                ]
            );

            register_rest_route(
                'wpr/v1',
                '/change',
                [
                    'methods' => 'POST',
                    'callback' => function (WP_REST_Request $request) {
                        $ulz_id = (int) $request->get_param('id');

                        if( empty( $ulz_id ) )
                            return $this->getAnswerApi(99, 'Ошибка, такого изображения не существует.', []);

                        $preview_id = json_decode(get_post_meta($ulz_id, 'ulz_gallery', true), true)[0]['id'];
                        $original_id = json_decode(get_post_meta($ulz_id, 'ulz_download', true), true)[0]['id'];

                        if( empty($preview_id ) || empty( $original_id ) )
                            return $this->getAnswerApi( 98, 'Ошибка, оригинал и/или превью изображения не существует.', [] );

                        $preview_file = get_attached_file( $preview_id );
                        $original_file = get_attached_file( $original_id );

                        if( empty( $preview_file ) || empty( $original_file ) )
                            return $this->getAnswerApi(97, 'Ошибка, оригинал и/или превью файлы изображений отсутствуют на сервере.', []);

                        $watermark_path = __DIR__ . '/Assets/Watermark.png';
                        $manager = new ImageManager(['driver' => 'gd']);

                        $attachment_metadata = wp_get_attachment_metadata( $preview_id );
                        $attachment_dir = get_post_meta( $preview_id, '_wp_attached_file', true );
                        $directory = dirname( $attachment_dir );
                        $upload_dir = wp_upload_dir();
                        $base_dir = trailingslashit($upload_dir['basedir']) . trailingslashit($directory);

                        foreach ($attachment_metadata['sizes'] as $size_type => $size_data) {
                            $image_path = $base_dir . $size_data['file']; // Полный путь к файлу размера

                            if (file_exists($image_path))
                            {
                                if( !copy( $original_file, $image_path ) )
                                    continue;

                                if( $size_type == 'medium' || $size_type == 'thumbnail' || $size_type == 'ulz_thumbnail' )
                                    continue;

                                $image = $manager->make( $image_path );
                                $image->resize( $size_data['width'], $size_data['height'] );
                                $watermark = $manager->make( $watermark_path );
                                $watermark->fit( $size_data['width'], $size_data['height'] );
                                $image->insert( $watermark, 'center' );
                                $image->save( $image_path );
                                $image->destroy();
                                $watermark->destroy();
                            }
                        }

                        return $this->getAnswerApi(0, 'Изображение изменено.', [] );
                    }
                ]
            );

        });
    }
}
