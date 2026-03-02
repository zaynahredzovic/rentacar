function submitForm(formId, url, type, formData, successCallback = null, errorCallback = null) {
    const form = document.getElementById(formId);

    if (!form){
        console.error(`Form with id ${formId} not found.`);
        return false;
    }

    console.log('Submitting form:', formId);
    console.log('URL:', url);
    console.log('Form data:', formData);

    // Show loading state
    const submitBtn = form.querySelector('button[type="submit"]');
    let originalText = '';

    if (submitBtn) {
        originalText = submitBtn.textContent;
        submitBtn.textContent = 'Submitting...';
        submitBtn.disabled = true;
    }

    // Convert formData object to URL encoded string
    const encodedData = $.param(formData);
    console.log('Encoded data:', encodedData);

    // Ajax request
    $.ajax({
        method: type,
        url: url,
        data: encodedData,
        dataType: 'json',
        success: function(response) {
            console.log(`${formId} success:`, response);
            
            if (successCallback && typeof successCallback === 'function') {
                successCallback(response, form);
            }
        },
        error: function(xhr, status, error) {
            console.error(`${formId} error:`, error);
            
            if (errorCallback && typeof errorCallback === 'function') {
                errorCallback(xhr, status, error, form);
            } else {
                const responseMsg = document.getElementById('responseMsg');
                if (responseMsg) {
                    responseMsg.className = 'error';
                    responseMsg.textContent = 'An error occurred. Please try again.';
                }
            }
        },
        complete: function() {
            if (submitBtn) {
                submitBtn.textContent = originalText;
                submitBtn.disabled = false;
            }
        }
    });

    return false;
}