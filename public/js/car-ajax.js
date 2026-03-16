// Car Ajax
const CarAjax = {
    addCar: function(form, successCallback, errorCallback) {
        const formData = new FormData(form);
        
        $.ajax({
            url: '/rentacar/api/cars',
            type: 'POST',
            data: formData,
            processData: false,
            contentType: false,
            dataType: 'json',
            success: successCallback,
            error: errorCallback
        });
    },
    
    loadCars: function(filter, successCallback) {
        $.ajax({
            url: '/rentacar/api/cars',
            type: 'GET',
            data: { filter: filter },
            dataType: 'json',
            success: successCallback
        });
    }
};