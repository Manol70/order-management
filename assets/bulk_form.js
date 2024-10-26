    document.addEventListener('turbo:load', function () { 
        function setupBulkButton(buttonId, statusType) {
            const button = document.getElementById(buttonId);
            if (button) {
                button.addEventListener('click', function () {
                    var checkboxes = document.querySelectorAll(`input[name="selectedOrders_${statusType}[]"]:checked`);
                    var selectedOrders = [];
                    var statusId = null;
                    var modalTitle = null;
                    
                    
                    checkboxes.forEach(function (checkbox) {
                        selectedOrders.push(checkbox.value); // Добавяне на ID-то на поръчката към масива
                        statusId = checkbox.getAttribute('data-status'); // Предполага се, че всички чекбоксове имат един и същ статус
                        modalTitle = checkbox.getAttribute('data-title');
                    });
    
                    if (selectedOrders.length === 0) {
                        alert('Моля, изберете поне една поръчка.');
                        return;
                    }
                    if (statusType=='status'){
                        var urls = selectedOrders.map(orderId => `/status/order?orderId=${orderId}`);
                    } else {
                    var urls = selectedOrders.map(orderId => `/status/${statusType}?orderId=${orderId}`);
                    }    
                    var modalCycleButton = document.createElement('button');
                    modalCycleButton.setAttribute('data-controller', 'bulk-status');
                    modalCycleButton.setAttribute('data-bulk-status-urls-value', JSON.stringify(urls));
                    modalCycleButton.setAttribute('data-bulk-status-status-id-value', statusId);
                    modalCycleButton.setAttribute('data-bulk-status-modal-title-value', modalTitle);
                    modalCycleButton.style.display = 'none';
    
                    document.body.appendChild(modalCycleButton);
    
                    modalCycleButton.click();
                });
            } else {
               // console.error(`Button with id ${buttonId} not found.`);
            }
        }
    
        function updateButtonStates() {
            var statusCheckboxes = document.querySelectorAll('input[name="selectedOrders_status[]"]:checked');
            var glassCheckboxes = document.querySelectorAll('input[name="selectedOrders_glass[]"]:checked');
            var detailCheckboxes = document.querySelectorAll('input[name="selectedOrders_detail[]"]:checked');
            var mosquitoCheckboxes = document.querySelectorAll('input[name="selectedOrders_mosquito[]"]:checked');

            var statusButton = document.getElementById('changeStatusButton');
            var glassButton = document.getElementById('changeStatusButtonGlass');
            var detailButton = document.getElementById('changeStatusButtonDetail');
            var mosquitoButton = document.getElementById('changeStatusButtonMosquito');
            if (statusButton && statusCheckboxes.length>0) {
                // Ако има избрани чекбоксове за статус, деактивирай бутоните за glass, detail, mosquito
                if(glassButton){
                    statusButton.disabled = false;
                    glassButton.disabled = true;
                } 
                if(detailButton){
                    statusButton.disabled = false;
                    detailButton.disabled = true;
                }
                if(mosquitoButton){
                    statusButton.disabled = false;
                    mosquitoButton.disabled = true;
                }
                
                   
            } 
            if (glassButton && glassCheckboxes.length>0) {
                // Ако има избрани чекбоксове за glass, деактивирай бутоните за status,detail,moasquito
                if(statusButton){
                    glassButton.disabled = false;
                    statusButton.disabled = true
                }   
                if(detailButton){
                    glassButton.disabled = false;
                    detailButton.disabled = true
                }
                if(mosquitoButton){
                    glassButton.disabled = false;
                    mosquitoButton.disabled = true;
                }   
            } 
            if (detailButton && detailCheckboxes.length>0) {
                // Ако има избрани чекбоксове за detail, деактивирай бутоните за status,glass,mosquito
                if(statusButton){
                    detailButton.disabled = false;
                    statusButton.disabled = true
                }   
                if(glassButton){
                    detailButton.disabled = false;
                    glassButton.disabled = true
                }
                if(mosquitoButton){
                    detailButton.disabled = false;
                    mosquitoButton.disabled = true;
                }   
            } 
            if (mosquitoButton && mosquitoCheckboxes.length>0) {
                // Ако има избрани чекбоксове за mosquito, деактивирай бутоните за status,glass,detail
                if(statusButton){
                    mosquitoButton.disabled = false;
                    statusButton.disabled = true
                }   
                if(glassButton){
                    mosquitoButton.disabled = false;
                    glassButton.disabled = true
                }
                if(detailButton){
                    mosquitoButton.disabled = false;
                    detailButton.disabled = true;
                }   
            } 
        }
    
        // Настройка на бутоните
        setupBulkButton('changeStatusButton', 'status');
        setupBulkButton('changeStatusButtonGlass', 'glass');
        setupBulkButton('changeStatusButtonDetail', 'detail');
        setupBulkButton('changeStatusButtonMosquito', 'mosquito');
        // Наблюдавай промените в чекбоксовете
        document.querySelectorAll('input[name="selectedOrders_status[]"], input[name="selectedOrders_glass[]"], input[name="selectedOrders_detail[]"], input[name="selectedOrders_mosquito[]"]').forEach(function (checkbox) {
            checkbox.addEventListener('change', updateButtonStates);
        });
    
        // Инициализиране на състоянието на бутоните при зареждане на страницата
        updateButtonStates();
    });










    


    