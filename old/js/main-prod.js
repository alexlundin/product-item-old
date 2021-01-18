// Получаем нужный элемент
// var element = document.querySelector('.btn-zoom');
let elements = document.querySelectorAll('.btn-zoom');


let Visible = function (target) {
    // Все позиции элемента
    let targetPosition = {
            top: window.pageYOffset + target.getBoundingClientRect().top,
            left: window.pageXOffset + target.getBoundingClientRect().left,
            right: window.pageXOffset + target.getBoundingClientRect().right,
            bottom: window.pageYOffset + target.getBoundingClientRect().bottom
        },
        // Получаем позиции окна
        windowPosition = {
            top: window.pageYOffset,
            left: window.pageXOffset,
            right: window.pageXOffset + document.documentElement.clientWidth,
            bottom: window.pageYOffset + document.documentElement.clientHeight
        };

    if (targetPosition.bottom > windowPosition.top && // Если позиция нижней части элемента больше позиции верхней чайти окна, то элемент виден сверху
        targetPosition.top < windowPosition.bottom && // Если позиция верхней части элемента меньше позиции нижней чайти окна, то элемент виден снизу
        targetPosition.right > windowPosition.left && // Если позиция правой стороны элемента больше позиции левой части окна, то элемент виден слева
        targetPosition.left < windowPosition.right) { // Если позиция левой стороны элемента меньше позиции правой чайти окна, то элемент виден справа
        // Если элемент полностью видно, то запускаем следующий код
        setTimeout(function () {

            target.classList.remove("start-zoom");
            target.classList.add("start-zoom");

        }, 500);

    } else {
        // Если элемент не видно, то запускаем этот код
        target.classList.remove("start-zoom");


    }
    ;
};

window.addEventListener('scroll', function () {
    for (let i = 0; i < elements.length; i++) { // проходим циклом по всем элементам объекта
        Visible(elements[i]); // устанавливаем красный цвет текста каждому элементу
    }

});

// А также запустим функцию сразу. А то вдруг, элемент изначально видно
for (let i = 0; i < elements.length; i++) { // проходим циклом по всем элементам объекта
    Visible(elements[i]); // устанавливаем красный цвет текста каждому элементу
}