function getChitsMonthsFromStartDate(startDate, noOfMonths) {
    let resultArr = [];
    const currentDate = new Date(startDate);

    for (let i = 0; i < noOfMonths; i++) {
        currentDate.setMonth(currentDate.getMonth() + 1);
        const nextMonth = currentDate.toLocaleDateString('en-CA', {
            year: 'numeric',
            month: '2-digit'
        }).replace('/', '-');
        // console.log(nextMonth);
        resultArr.push(nextMonth);
    }
    return resultArr;
}

let calculateChitDates = function (input) {
    let noOfChits = $("#no-of-chits").val();
    let startDate = $("#start-date").val();
    if (noOfChits == undefined || noOfChits == "") {
        alert("❗ Please enter No Of Chits before Start Date");
        return false;
    }
    noOfChits = parseInt(noOfChits);
    let chitMonths = getChitsMonthsFromStartDate(startDate, noOfChits);
    for (let i = 0; i < noOfChits; i++) {
        $("#chit-date-" + i).val(chitMonths[i]);
    }
    $("#end-date").val(chitMonths[(noOfChits - 1)] + '-01');
};

let memberSelectChanged = function (select) {
    // addMembers();
};

let deleteRow = function (rowId) {
    $("#member-row-" + rowId).remove();
    reconfigureSerialNumbers(false);
};

let reconfigureSerialNumbers = function (showAlert) {
    let rowNum = parseInt($("#row-num").val());
    let noOfMembers = parseInt($("#no-of-members").val());
    let incNum = 1;
    for (let i = 0; i < rowNum; i++) {
        if ($("#member-row-" + i).length > 0) {
            $("#member-no-" + i).text(incNum);
            if (showAlert) {
                if (incNum > noOfMembers) {
                    alert("❌ You are adding more than given members!");
                    $("#member-row-" + i).remove();
                }
            }
            incNum = incNum + 1;
        }
    }
};

let addMembers = function () {
    // let memberNameMobile = $("#member-select").val();
    let data = $('#member-select').select2('data');
    let memberNameMobile = data[0]['text'];
    let memberId = data[0]['id'];

    if (memberId == "") {
        // Do Nothing
    } else {
        memberNameMobile = memberNameMobile.split(" - ");
        let memberName = memberNameMobile[0];
        let memberMobile = memberNameMobile[1];
        let rowNum = parseInt($("#row-num").val());
        let htmlString = '<div class="row add-border-bottom" id="member-row-' + rowNum + '"> ' +
            '<div class="col-sm-1 top-1">' +
            '<span id="member-no-' + rowNum + '"></span>' +
            '</div>' +
            '<div class="col-sm-1 top-1 hide">' +
            '<span id="member-id-' + rowNum + '">' + memberId + '</span>' +
            '</div>' +
            '<div class="col-sm-4 top-1">' +
            '<span id="member-name-' + rowNum + '">' + memberName + '</span>' +
            '</div>' +
            '<div class="col-sm-4 top-1">' +
            '<span id="member-mobile-' + rowNum + '"> ' + memberMobile + '</span>' +
            '</div>' +
            '<div class="col-sm-2 top-1" id="member-delete-' + rowNum + '">' +
            '<button type="button" class="btn btn-danger btn-xs" onclick=deleteRow(' + rowNum + ')>' +
            '<i class="fa fa-trash-o" aria-hidden="true"></i>' +
            '</button>' +
            '</div>' +
            '</div>';
        $("#members-div").append(htmlString);
        $("#row-num").val(rowNum + 1);
        reconfigureSerialNumbers(true);
        $("#member-select").select2().val("").trigger("change");
    }
};

let getMembersObject = function () {
    let rowNum = parseInt($("#row-num").val());
    let noOfMembers = parseInt($("#no-of-members").val());
    let resultArr = [];
    for (let i = 0; i < rowNum; i++) {
        let resultObj = {};
        if ($("#member-row-" + i).length > 0) {
            resultObj["MEMBER_ID"] = $("#member-id-" + i).text();
            resultObj["MEMBER_NAME"] = $("#member-name-" + i).text();
            resultObj["MEMBER_MOBILE"] = $("#member-mobile-" + i).text();
            resultArr.push(resultObj);
        }
    }
    return resultArr;
};

let getChitAmountDetailsForChitsMaster = function () {
    let resArr = [];
    let noOfChits = $("#no-of-chits").val();
    if (isNaN(parseInt(noOfChits))) {
        alert("❌ Please enter a valid number for No Of Chits");
        $("#no-of-chits").val('');
        return false;
    } else {
        noOfChits = parseInt(noOfChits);
        for (let i = 0; i < noOfChits; i++) {
            let dataObj = {};
            let monthlyAmount = $("#chit-amount-" + i).val();
            let kasarAmount = $("#chit-kasar-" + i).val();
            let totalAmount = $("#total-chit-amount-" + i).val();
            if (monthlyAmount == "") {
                alert("⚠️ சீட்டு தொகைக்கான அனைத்து மதிப்புகளையும் உள்ளிடவும்");
                return false;
            } else if (kasarAmount == "") {
                alert("⚠️ கசர் தொகைக்கான அனைத்து மதிப்புகளையும் உள்ளிடவும்");
                return false;
            } else if (totalAmount == "") {
                alert("⚠️ பட்டுவாடா தொகைக்கான அனைத்து மதிப்புகளையும் உள்ளிடவும்");
                return false;
            } else {
                dataObj["MONTHLY_AMOUNT"] = monthlyAmount;
                dataObj["KASAR_AMOUNT"] = kasarAmount;
                dataObj["TOTAL_AMOUNT"] = totalAmount;
                resArr.push(dataObj);
            }
        }

    }
    return resArr;
};

let getChitAmountDetails = function () {
    let resArr = [];
    let noOfChits = $("#no-of-chits").val();
    if (isNaN(parseInt(noOfChits))) {
        alert("❌ Please enter a valid number for No Of Chits");
        $("#no-of-chits").val('');
        return false;
    } else {
        noOfChits = parseInt(noOfChits);
        for (let i = 0; i < noOfChits; i++) {
            let dataObj = {};
            let chitMonth = $("#chit-date-" + i).val();
            let monthlyAmount = $("#chit-amount-" + i).val();
            let kasarAmount = $("#chit-kasar-" + i).val();
            let totalAmount = $("#total-chit-amount-" + i).val();
            if (chitMonth == "") {
                alert("⚠️ சீட்டு மாதத்திற்கான அனைத்து மதிப்புகளையும் உள்ளிடவும்");
                return false;
            } else if (monthlyAmount == "") {
                alert("⚠️ சீட்டு தொகைக்கான அனைத்து மதிப்புகளையும் உள்ளிடவும்");
                return false;
            } else if (kasarAmount == "") {
                alert("⚠️ கசர் தொகைக்கான அனைத்து மதிப்புகளையும் உள்ளிடவும்");
                return false;
            } else if (totalAmount == "") {
                alert("⚠️ பட்டுவாடா தொகைக்கான அனைத்து மதிப்புகளையும் உள்ளிடவும்");
                return false;
            } else {
                dataObj["CHIT_MONTH"] = chitMonth;
                dataObj["MONTHLY_AMOUNT"] = monthlyAmount;
                dataObj["KASAR_AMOUNT"] = kasarAmount;
                dataObj["TOTAL_AMOUNT"] = totalAmount;
                resArr.push(dataObj);
            }
        }

    }
    return resArr;
};

let noOfChitsChanged = function (input) {
    let noOfChits = $(input).val();
    if (isNaN(parseInt(noOfChits))) {
        alert("🚫 Please enter a valid number!");
        $(input).val('');
        return false;
    } else {
        noOfChits = parseInt(noOfChits);
        $('#chit-details-body').empty();

        for (let i = 0; i < noOfChits; i++) {
            let htmlString = '<div class="row bdr-btm-orange t-1-5" id="chit-row-' + i + '">' +
                '<div class="col-sm-1">' +
                '<span id="chit-sno-' + i + '">' + (i + 1) + '</span>' +
                '</div>' +
                '<div class="col-sm-4">' +
                '<div class="form-group">' +
                '<input type="number" class="form-control" id="chit-amount-' + i + '">' +
                '</div>' +
                '</div>' +
                '<div class="col-sm-3">' +
                '<div class="form-group">' +
                '<input type="number" class="form-control" id="chit-kasar-' + i + '">' +
                '</div>' +
                '</div>' +
                '<div class="col-sm-4">' +
                '<div class="form-group">' +
                '<input type="number" class="form-control" id="total-chit-amount-' + i + '">' +
                '</div>' +
                '</div>' +
                '</div>';
            $("#chit-details-body").append(htmlString);
        }
        $("#accordion").click();
    }
};

let noOfMembersChanged = function (input, chitAmountDetails) {
    let noOfMembers = $(input).val();
    if (isNaN(parseInt(noOfMembers))) {
        alert("🚫 Please enter a valid number!");
        $(input).val('');
        return false;
    } else {
        $("#start-date").val('');
        $("#end-date").val('');
        noOfMembers = parseInt(noOfMembers);
        $("#no-of-chits").val(noOfMembers);
        $('#chit-details-body').empty();
        for (let i = 0; i < noOfMembers; i++) {
            let monthlyAmount = 0;
            try { monthlyAmount = chitAmountDetails[i]["MONTHLY_AMOUNT"]; } catch (e) { monthlyAmount = ''; }
            let kasarAmount = 0;
            try { kasarAmount = chitAmountDetails[i]["KASAR"]; } catch (e) { kasarAmount = ''; }
            let totalAmount = 0;
            try { totalAmount = chitAmountDetails[i]["TOTAL_AMOUNT"]; } catch (e) { totalAmount = ''; }

            let htmlString = '<div class="row bdr-btm-orange t-1-5" id="chit-row-' + i + '">' +
                '<div class="col-sm-1">' +
                '<span id="chit-sno-' + i + '">' + (i + 1) + '</span>' +
                '</div>' +
                '<div class="col-sm-3">' +
                '<div class="form-group">' +
                '<input type="month" class="form-control" id="chit-date-' + i + '">' +
                '</div>' +
                '</div>' +
                '<div class="col-sm-3">' +
                '<div class="form-group">' +
                '<input type="number" class="form-control" id="chit-amount-' + i + '" value="' + monthlyAmount + '">' +
                '</div>' +
                '</div>' +
                '<div class="col-sm-2">' +
                '<div class="form-group">' +
                '<input type="number" class="form-control" id="chit-kasar-' + i + '" value="' + kasarAmount + '">' +
                '</div>' +
                '</div>' +
                '<div class="col-sm-3">' +
                '<div class="form-group">' +
                '<input type="number" class="form-control" id="total-chit-amount-' + i + '" value="' + totalAmount + '">' +
                '</div>' +
                '</div>' +
                '</div>';
            $("#chit-details-body").append(htmlString);
        }
    }
};