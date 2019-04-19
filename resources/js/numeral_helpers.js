import numeral from 'numeral'

numeral.register('locale', 'id', {
    delimiters: {
        thousands: '.',
        decimal: ','
    },
    abbreviations: {
        thousand: 'rb',
        million: 'jt',
        billion: 'm',
        trillion: 't'
    },
    ordinal : function (number) {
        return '';
    },
    currency: {
        symbol: 'Rp.'
    }
});

numeral.locale('id')

function number_format(value) {
    const orig_locale = numeral.locale()

    numeral.locale('en')
    const converted = numeral(value).value()
    numeral.locale(orig_locale)

   return  numeral(converted).format('0,0.[0000]')
}

module.exports = { numeral, number_format }