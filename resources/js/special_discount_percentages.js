function special_discount_percentages() {
    let percentages = []
    for (let i = 0; i <= 100; i+=5) {
        percentages.push(i / 100)
    }

    return percentages
}

module.exports = special_discount_percentages()