class DataManager
{
    async getIds()
    {
        let resultArray = null;

        await fetch( '/wp-json/wpr/v1/getlists', { method: 'POST' } )
        .then( response => response.json() )
        .then( data => { resultArray = data.content; } )
        .catch( error => { console.error( 'Произошла ошибка:', error ); } );
        return resultArray;
    }

    // Модифицированный метод sendData, возвращающий промис
    async sendData(id) {
      const formData = new FormData();
      formData.append('id', id);

      return fetch('/wp-json/wpr/v1/change', {
        method: 'POST',
        body: formData,
      })
        .then(response => response.json())
        .then(data => {
          console.log(data.message);
          return data; // Возвращаем данные в цепочку промисов
        })
        .catch(error => {
          console.error(error);
          throw error; // Пробрасываем ошибку в цепочку промисов
        });
    }
}
