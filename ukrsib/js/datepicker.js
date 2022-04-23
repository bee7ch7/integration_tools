
  jQuery(function ($) {
    $.datepicker.regional['ua'] = {
        closeText: 'Закрити',
        prevText: '&#x3c;Поп',
        nextText: 'Наст&#x3e;',
        currentText: 'Сьогодні',
        monthNames: ['Січень', 'Лютий', 'Березень', 'Квітень', 'Травень', 'Червень',
        'Липень', 'Серпень', 'Вересень', 'Жовтень', 'Листопад', 'Грудень'],
        monthNamesShort: ['Січ', 'Лют', 'Бер', 'Квіт', 'Трав', 'Черв',
        'Лип', 'Серп', 'Вер', 'Жовт', 'Лист', 'Груд'],
        dayNames: ['неділя', 'понеділок', 'вівторок', 'среда', 'четвер', 'п\'ятница', 'субота'],
        dayNamesShort: ['нд', 'пн', 'вт', 'ср', 'чт', 'пт', 'сб'],
        dayNamesMin: ['Нд', 'Пн', 'Вт', 'Ср', 'Чт', 'Пт', 'Сб'],
        weekHeader: 'Ти',
        dateFormat: 'dd-mm-yy',
        firstDay: 1,
        isRTL: false,
        showMonthAfterYear: false,
        yearSuffix: '',
		minDate: "-12M",
		maxDate: "+1M",
		showOtherMonths: true,
        selectOtherMonths: true
    };
    $.datepicker.setDefaults($.datepicker.regional['ua']);
});


  $(document).ready(function() {
        $("#date_from").datepicker();
		$("#date_to").datepicker();

  });
