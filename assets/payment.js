/*document.addEventListener('turbo:load', function() {
    processPayment()
    
});*/
//горния код е закоментиран, за да спрем turbo и е заменен с този:
document.addEventListener('DOMContentLoaded', function() {
    processPayment();
});


function processPayment(exclamationCount) {
     console.log('jQuery version:', $.fn.jquery);
    
    // Тестване на jQuery
    $(document).ready(function () {
    // Пример за извикване на jQuery функционалност
    //$('body').css('background-color', 'lightblue');
    console.log('Body background color changed!');
    });
    
    
    console.log('Payment.js is loaded!');
    
    
    
    var totalAmount = 0;
    var orderPayments = [
        //  масив за предаване към контролера
    ];
    var paymentDoc = '';
    var docNumber = '';
    
    var requestData = JSON.stringify(orderPayments);
    console.log('requestData', requestData);
    document.addEventListener('DOMContentLoaded', function () {
               
        $(document).on('click', '.transferAmountButton', function () {
            var orderId = $(this).data('order-id');
            console.log('orderId:', orderId);
            transferAmount(orderId);
                    });
    
        $('.paymentInput').on('input', function () {
            
            var orderId = $(this).data('id');
            var orderNumber = $(this).data('order-number')
            var paymentDoc = $(this).closest('tr').find('.paymentDoc').val() || '';
            var docNumber = $(this).closest('tr').find('.docNumber').val() || '';

            orderNumber = parseInt(orderNumber);
            console.log(orderNumber);
            var orderRow = $('#orderTable tr[data-id="' + orderId + '"]');
            console.log('orderRow:', orderRow);
            var paymentAmount = parseFloat($(this).val().replace(',', '.')) || 0;
            
            console.log('paymentAmount:', paymentAmount)
            var price = parseFloat(orderRow.data('price'));
            console.log('price:', price)
            // Задаване на оригиналната totalAmount преди промяната
            var originalTotalAmount = totalAmount;
            if ($(this).val().trim() === '') {
                paymentAmount = 0;
            }    
            // Задаване на оригиналната цена преди промяната
            var originalPrice = parseFloat(orderRow.data('price'));
            
            console.log("originalTotalAmount:", originalTotalAmount);
            
            if (!isNaN(paymentAmount) && paymentAmount >= 0 && paymentAmount <= price) {
                var currentPayment = parseFloat(orderRow.data('payment')) || 0;
                console.log('currentPayment:', currentPayment);
                totalAmount = totalAmount - currentPayment + paymentAmount;
                orderRow.data('payment', paymentAmount);
                var newPrice = price - paymentAmount;
    
                // Зануляване на сумата в колоната "Цена" при прехвърляне
                if (paymentAmount > 0) {
                    newPrice = 0;
                }
    
                // Актуализираме "цена" според въведената сума в "плащане"
                actualPrice = price - paymentAmount;
                orderRow.find('td[data-price]').text(actualPrice.toFixed(2));
                console.log('orderRow:',orderRow)
                console.log('actualPrice:', actualPrice)
                var orderNumber = document.querySelector(`button[data-order-id="${orderId}"]`).dataset.orderNumber;
                // Превръщане на orderNumber в число
                orderNumber = parseInt(orderNumber);
                console.log(orderNumber);
                $(document).ready(function () {
                    // Функция, която активира или деактивира полетата за документ за плащане в зависимост от въведената сума
                    function togglePaymentDoc() {
                        $('.paymentDoc').each(function () {
                            
                            //console.log('paymentDoc:', paymentDoc);
                            var paymentAmount = parseFloat($(this).closest('tr').find('.paymentInput').val().replace(',', '.')) || 0;
                            //console.log('paymentAmountttttt:', paymentAmount);
                            var paymentDocInput = $(this);
                            if (paymentAmount > 0) {
                                paymentDocInput.prop('disabled', false);
                            } else {
                                paymentDocInput.prop('disabled', true);
                                // Изчистване на стойността в полето за документ за плащане
                                paymentDocInput.val('');
                                paymentDoc = paymentDocInput;
                            
                            }
                        });
                    }

                    // Функция, която активира или деактивира полетата за номер на документ за плащане
                    function toggleDocNumber() {
                        $('.docNumber').each(function () {
                            var paymentAmount = parseFloat($(this).closest('tr').find('.paymentInput').val().replace(',', '.')) || 0;
                            var docNumberInput = $(this);
                            if (paymentAmount > 0) {
                                docNumberInput.prop('disabled', false);
                            } else {
                                docNumberInput.prop('disabled', true);
                                docNumberInput.val('');
                            }
                        });
                    }
                
                    // Извикване на функцията, която активира или деактивира полетата за документ за плащане в зависимост от въведената сума
                    togglePaymentDoc();
                    // Извикване на функцията, която активира или деактивира полетата за номер на документ
                    toggleDocNumber();
                    
                    // Събитие, което се извиква при въвеждане на данни в полетата за сума
                    $('.paymentInput').on('input', function () {
                        var paymentDoc = $(this).closest('tr').find('.paymentDoc').val() || '';
                        console.log('payDodooo:', paymentDoc);
                        togglePaymentDoc(paymentDoc);
                        toggleDocNumber();
                    });
                
                    // Събитие, което се извиква при въвеждане на данни в полетата за документ за плащане и номер на док.
                    $('.paymentDoc, .docNumber').on('input', function () {
                        //var paymentDoc = $(this).val() || ''; този код е когато се следи за събтие само в paymentDoc
                        var paymentDoc = $(this).closest('tr').find('.paymentDoc').val() || '';
                        var docNumber = $(this).closest('tr').find('.docNumber').val() || ''; // Вземете стойността от полето docNumber
                        var orderId = $(this).closest('tr').data('id');
                        var orderNumber = $(this).closest('tr').data('order-number');
                        var paymentAmount = parseFloat($(this).closest('tr').find('.paymentInput').val().replace(',', '.')) || 0;
                        

                        // Проверка дали има въведена сума за плащане
                        if (paymentAmount < 0.01) {
                            $(this).prop('disabled', true);
                            $(this).closest('tr').find('.paymentDocError').text('Не може да въвеждате без посочена сума за плащане');
                        } else {
                            $(this).prop('disabled', false);
                            $(this).closest('tr').find('.paymentDocError').text('');
                        }
                
                        var orderIndex = orderPayments.findIndex(order => order.orderId === orderId);
                        if (orderIndex !== -1) {
                            orderPayments[orderIndex].paymentAmount = paymentAmount;
                            orderPayments[orderIndex].paymentDoc = paymentDoc;
                        } else {
                            orderPayments.push({ orderId: orderId, paymentAmount: paymentAmount, orderNumber: orderNumber, paymentDoc: paymentDoc, docNumber: docNumber });
                        }
                
                        updateOrderPayments(orderId, paymentAmount, orderNumber, paymentDoc, docNumber);
                    });

                    
                });

                updateOrderPayments(orderId, paymentAmount, orderNumber, paymentDoc, docNumber);
                console.log('After updateOrderPayments:', orderPayments);
                // Събиране на стойностите от масива и актуализиране на totalAmount
                var totalFromPayments = orderPayments.map(function(payment) {
                    return payment.paymentAmount;
                }).reduce(function (acc, paymentAmount) {
                return acc + paymentAmount;
                }, 0);
                console.log('totalFromPayments:', totalFromPayments);
                //totalAmount = originalTotalAmount - totalFromPayments;
                $('#totalAmount').text(totalFromPayments.toFixed(2));   
            } else {
                console.log('price:', price);
                console.log('actualPrice:', actualPrice);
                
                // Въвеждането е невалидно - например, изчистване полето или показвание съобщение за грешка
                $(this).val(''); // Изчистване на полето
                // Възстановяване на оригиналната цена при грешка
                orderRow.find('td[data-price]').text(originalPrice.toFixed(2));
                // Възстановяване на оригиналната totalAmount при грешка
                var greshka= price - actualPrice;
                console.log('greshka:', greshka);    
                totalAmount = originalTotalAmount - greshka;
                $('#totalAmount').text(totalAmount.toFixed(2));
                // Изтриване на последния елемент от масива, тъй като съответната поръчка не е валидна
                orderPayments.pop();
                // Събиране на стойностите от масива и актуализиране на totalAmount
                var totalFromPayments = orderPayments.map(function(payment) {
                return payment.paymentAmount;
                }).reduce(function (acc, paymentAmount) {
                return acc + paymentAmount;
                }, 0);
                console.log('totalFromPayments:', totalFromPayments);
                //totalAmount = originalTotalAmount - totalFromPayments;
                $('#totalAmount').text(totalFromPayments.toFixed(2));
                console.log('ordersPayments else:', orderPayments);
                alert('Моля, въведете валидна сума. Сумата не може да е по-голяма от дължимата цена, както и да е отрицателна стойност');
                //добавяне на друга логика
                console.log('orderPaymentsBeforeupdategreshka', orderPayments);
                paymentAmount = 0;
                updateOrderPayments(orderId, paymentAmount, orderNumber, paymentDoc, docNumber);
                console.log('orderPaymentsAftergreshka', orderPayments);
            }
        });

        function updateOrderPayments(orderId, paymentAmount, orderNumber, paymentDoc, docNumber) {
            // Проверка дали поръчката вече е включена в масива
            var orderIndex = orderPayments.findIndex(order => order.orderId === orderId);
            //console.log('paymentDoc:', paymentDoc);
            if (orderIndex !== -1) {
                console.log('orderPaymentsIF:', orderPayments)
                // Ако поръчката е вече в масива, актуализираме стойността
                orderPayments[orderIndex].paymentAmount = paymentAmount;
                orderPayments[orderIndex].paymentDoc = paymentDoc; // Добавяне на paymentDoc към масива
                orderPayments[orderIndex].docNumber = docNumber; // Добавяне на docNumber към масива

                // Изтриване на обекта от масива, ако paymentAmount е нула
                if (paymentAmount === 0) {
                    console.log('orderPaymentsIFIF:', orderPayments)
                    orderPayments.splice(orderIndex, 1);
                }
            } else {
                console.log('orderPaymentsElse:', orderPayments)
                //orderPayments[orderIndex].paymentDoc = paymentDoc;
                // Ако поръчката не е включена в масива, я добавяме
                orderPayments.push({ orderId: orderId, paymentAmount: paymentAmount, orderNumber: orderNumber, paymentDoc: paymentDoc, docNumber: docNumber});
                if (paymentAmount === 0) {
                    console.log('orderPaymentsIFIF:', orderPayments)
                    orderPayments.splice(orderIndex, 1);
                }
            }
    
            // Примерен код за изпращане на масива с данните към контролера
            console.log(orderPayments);
            var orderPaymentsJson = JSON.stringify(orderPayments);
            console.log(orderPaymentsJson);
            var form = document.querySelector('form[name="payment"]');
            // проверка дали има създадено скрито поле, ако няма се създаве и се обновява на стойността на скритото поле
            var hiddenInput = form.querySelector('input[name="orderPaymentsJson"]');
            if (!hiddenInput) {
            hiddenInput = document.createElement('input');
            hiddenInput.type = 'hidden'; // можем да променим типа на полето на text, ако искаме да видим какъв масив изпраща
            hiddenInput.name = 'orderPaymentsJson';
            form.appendChild(hiddenInput);
            }
            hiddenInput.value = orderPaymentsJson;

        }
    
        function transferAmount(orderId) {
            var orderRow = $('#orderTable tr[data-id="' + orderId + '"]');
            var price = parseFloat(orderRow.data('price'));
    
            // Задаване на въведената сума да бъде равна на цялата сума от колоната "Цена"
            orderRow.find('.paymentInput').val(price.toFixed(2)).trigger('input');
            
            // Повторно извикване на кода за обработка на въведената сума
            processPayment(1);  // Примерно извикване на обработката на сумата
            
        }
        // Функция за обновяване на orderPaymentsJson и стойността на скритото поле
        function updateOrderPaymentsJson() {
            orderPaymentsJson = JSON.stringify(orderPayments);
            hiddenInput.value = orderPaymentsJson;
        }



        $('form').on('submit', function () {
            // Изпращане на формата
            // Нулиране на данните в полетата
            $('.paymentInput').val('');
            $('.paymentDoc').val('');
            $('.docNumber').val('');
        });
    
        function processPayment(exclamationCount) {
            console.log(`Payment processed with ${exclamationCount} exclamation marks! Total Amount: ${totalAmount.toFixed(2)}`);
        }
    
        processPayment(3);

    });
    
    // Извикване на функцията извън контекста на DOMContentLoaded
    console.log(`Payment processed with 2 exclamation marks!`);
    console.log('Payment processed:', totalAmount);
    console.log('totalAmount:', totalAmount);
    console.log($('#totalAmount')); // Трябва да върне обекта със съответния елемент
    
    }
    module.exports = processPayment; 
    