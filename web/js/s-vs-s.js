 ymaps.ready(function () {
    let manager, isAdd = false;

    // строким карту
    let map = new ymaps.Map("YMapsID", {
      center: [56.802692, 59.931890],
      zoom: 15
    });

    if (map) {
      manager = new ymaps.LoadingObjectManager('/map-data?coords=%b', {
        clusterize: true,
      });
      map.geoObjects.add(manager);
      // клик по ракте отправляет запрос на добавление нового сотрудника
      map.events.add('click', (e) => {
        if (!isAdd) {
          return;
        }
        // проверка заполненности полей ...
        if (!$('select#region-el').val() || !$('select#post-el').val()) {
          return;
        }

        let d = $('form#filter-form').serialize();
        coords = e.get('coords');
        for (v in coords) {
          d += `&Employee[coordinate][${v}]=${coords[v]}`;
        }

        $.post('/map-put-data', d, (ret) => {
          if (ret.ok) {
            manager.reloadData();
          }
        });
      });
    }

    // реализация фильтра объектов ..на карте
    function filterApply() {
      region = $('select#region-el').val();
      post = $('select#post-el').val();
      manager.setFilter((obj) => {
        let ret = true;
        if (region) {
          ret = ret && obj.ObjData.region_val == region;
        }
        if (post.length > 0) {
          ret = ret && post.indexOf(obj.ObjData.post_val) != -1;
        }
        return ret;
      });
    }


    // Обнова по фильтру ...
    $('select#region-el, select#post-el').on('change', (e) => {
      if (!isAdd) {
        filterApply();
      }
    });

    // Изменение работы формы ... фильтр/добавление
    $('body').on('change-type-form', (e) => {
      isAdd = $('#add-item-flag').prop('checked');
      if (isAdd) {
        $('select#post-el').removeAttr('multiple');
      } else {
        $('select#post-el').attr('multiple', 'multiple');
      }
    })
  });