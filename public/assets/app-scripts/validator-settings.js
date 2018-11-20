

$.validator.addMethod(
    'greaterThanEqualToDMY',
    function(value, element, params) {

        if(value == '')
        {
            return true;
        }

        var target = $(params).val();

        var isValueNumeric = !isNaN(parseFloat(value)) && isFinite(value);
        var isTargetNumeric = !isNaN(parseFloat(target)) && isFinite(target);
        if (isValueNumeric && isTargetNumeric) {
            return Number(value) > Number(target);
        }

        targetDateArr = target.split('/');
        valueDateArr =value.split('/');

        targetDate = new Date( targetDateArr[2], (targetDateArr[1]-1),targetDateArr[0] );
        valueDate = new Date( valueDateArr[2], (valueDateArr[1]-1),valueDateArr[0] );

        if (!/Invalid|NaN/.test(valueDate)) {
            return new Date(valueDate) >= new Date(targetDate);
        }

        return false;
    },
    'Must be greater than {0}.');