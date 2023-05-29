window.addEventListener('load', function () {
  (async () => {
    const dataManager = new DataManager();
    const progressElement = document.querySelector('.wrp-progress-bar');
    const startElement = document.querySelector('.wrp-start');
    let ids = await dataManager.getIds();

    if (ids == null) {
      progressElement.innerHTML = 'Не удалось получить список изображений. Перезапустите страницу';
      startElement.disabled = true;
      return;
    }

    progressElement.innerHTML += ` Всего получено id: ${ids.length}.`;
    const progressBar = new ProgressBar('wrp-progress-bar', 0, ids.length);

    startElement.addEventListener('click', async function () {
      if (ids.length <= 0) {
        console.log('Не получены id изображения!');
        return;
      }

      this.disabled = true;

      console.log('Click');

      for (let id of ids) {
        try {
          await dataManager.sendData(id);
          progressBar.next();
          progressBar.render();
        } catch (error) {
          console.error(error);
        }
      }

      alert('Все изображения успешно созданы');
      this.disabled = false;
    });
  })();
});
