/*document.addEventListener('DOMContentLoaded', function () {
    console.log('JavaScript зареден успешно.');
    var changeStatusButton = document.getElementById('changeStatusButton');


    // Проверка дали бутонът съществува
    if (changeStatusButton) {
    changeStatusButton.addEventListener('click', function () {
        console.log('Бутонът е натиснат.');
        var checkboxes = document.querySelectorAll('input[name="selectedOrders[]"]:checked');
        var selectedOrders = [];
        var statusId = null;

        checkboxes.forEach(function (checkbox) {
            selectedOrders.push(checkbox.value); // Добавяне на ID-то на поръчката към масива
            statusId = checkbox.getAttribute('data-status'); // Предполага се, че всички чекбоксове имат един и същ статус
        });

        if (selectedOrders.length === 0) {
            alert('Моля, изберете поне една поръчка.');
            return;
        }

        // Създаване на динамичен формуляр
        var form = document.createElement('form');
        form.method = 'POST';
        form.action = changeStatusButton.getAttribute('data-url');

        // Добавяне на скрити полета за избраните поръчки
        selectedOrders.forEach(function (orderId) {
            var orderInput = document.createElement('input');
            orderInput.type = 'hidden';
            orderInput.name = 'selectedOrders[]';
            orderInput.value = orderId; // Тук orderId идва от selectedOrders масива
            form.appendChild(orderInput);
        });

        // Добавяне на скрито поле за статуса
        var statusInput = document.createElement('input');
        statusInput.type = 'hidden';
        statusInput.name = 'statusId';
        statusInput.value = statusId;
        form.appendChild(statusInput);

        // Добавяне на формуляра към документа и изпращането му
        document.body.appendChild(form);
        form.submit();
    });
}
});
*/

document.addEventListener('DOMContentLoaded', function () {
    //викаме функцията safeEventListener за проверка дали съществува id=changeStatusButon
    // и ако съществува, ще се изпълни долния код, за да не ползваме IF
    safeEventListener('#changeStatusButton', 'click', function() {
        var checkboxes = document.querySelectorAll('input[name="selectedOrders[]"]:checked');
        var selectedOrders = [];
        var statusId = null;
        var modalTitle = null;

        checkboxes.forEach(function (checkbox) {
            selectedOrders.push(checkbox.value); // Добавяне на ID-то на поръчката към масива
            statusId = checkbox.getAttribute('data-status'); // Предполага се, че всички чекбоксове имат един и същ статус
            modalTitle = checkbox.getAttribute('data-title');
        });
        console.log('selectedOrders:', selectedOrders);
        console.log('statusId:', statusId);
        console.log('modalTitle:', modalTitle);
        if (selectedOrders.length === 0) {
            alert('Моля, изберете поне една поръчка.');
            return;
        }

        // Генериране на URL-ите
        var urls = selectedOrders.map(orderId => `/status/order?orderId=${orderId}`);
        console.log('url:', urls);

        

        // Създаване на нов бутон с динамични атрибути
        var modalCycleButton = document.createElement('button');
        modalCycleButton.setAttribute('data-controller', 'bulk-status');
        modalCycleButton.setAttribute('data-bulk-status-urls-value', JSON.stringify(urls));
        modalCycleButton.setAttribute('data-bulk-status-status-id-value', statusId);
        modalCycleButton.setAttribute('data-bulk-status-modal-title-value', modalTitle);
        modalCycleButton.style.display = 'none'; // Скриваме бутона от потребителя


        document.body.appendChild(modalCycleButton);

        // Симулиране на клик върху новия бутон
        modalCycleButton.click();
    });
});
//функция за проверка дали даден елемент съществува/вместо да се ползва "if"
function safeEventListener(selector, event, handler) {
    const element = document.querySelector(selector);
    if (element) {
        element.addEventListener(event, handler);
    }
}