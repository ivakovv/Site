// Функция для переключения состояния переключателя
function switchNext() {
    // Получаем все переключатели
    const switches = [
        document.getElementById('switch1'),
        document.getElementById('switch2'),
        document.getElementById('switch3'),
        document.getElementById('switch4')
    ];

    // Находим индекс текущего активного переключателя
    const currentIndex = switches.findIndex(switchEl => switchEl.checked);

    // Вычисляем следующий индекс (по кругу)
    const nextIndex = (currentIndex + 1) % switches.length;

    // Снимаем отметку со всех переключателей
    switches.forEach(switchEl => switchEl.checked = false);

    // Устанавливаем отметку на следующий переключатель
    switches[nextIndex].checked = true;
}

// Эмулируем нажатие на правую кнопку каждые 4 секунды
setInterval(switchNext, 4000);