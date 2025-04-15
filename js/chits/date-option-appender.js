function formatDate(strDate) {
    const date = new Date(strDate);
    if (isNaN(date.getTime())) { // Check if the date is valid
        return "Invalid Date";
    }
    const options = {
        year: 'numeric',
        month: 'short'
    };
    return date.toLocaleDateString('en-US', options);
}

let appendOptionsToMonthSelect = function (dateArray) {
    $('#month-select option:not(:first)').remove();
    for (let i = 0; i < dateArray.length; i++) {
        $('#month-select')
            .append($("<option></option>")
                .attr("value", dateArray[i]["CHIT_MONTH"])
                .text(formatDate(dateArray[i]["CHIT_MONTH"])));
    }
};