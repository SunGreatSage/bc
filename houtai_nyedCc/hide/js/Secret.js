function callSecret(info) {
    var result = callSecretBase(info);
    return result.vk;
}

function callSecretBase(info) {
    var pk, pke;
    var logpk;
    logpk = "";
    $.ajax({
        url: SESSIONID + "/Member/GK",
        type: "post",
        dataType: "json",
        async: false,
        success: function (x) {
            if (x.Status == 1) {
                pk = x.Data.m;
                pke = x.Data.e;
                logpk = x.Data.e + ',' + x.Data.m;
                setMaxDigits(1290);
                var key = new RSAKeyPair(pke, "", pk);
                vk = encryptedString(key, info);
            } else {
                alert(x.Data);
            }
        }
    });
    return { vk: vk, logpk: logpk };
}

function callSecretBase2(info,x) {
    var pk, pke;
    var logpk;
    logpk = "";

            if (x.Status == 1) {
                pk = x.Data.m;
                pke = x.Data.e;
                logpk = x.Data.e + ',' + x.Data.m;
                setMaxDigits(1290);
                var key = new RSAKeyPair(pke, "", pk);
                vk = encryptedString(key, info);
            } else {
                alert(x.Data);
            }

    return { vk: vk, logpk: logpk };
}

function callLogDoLogin(logpk) {
    $.ajax({
        url: SESSIONID + "/Member/LogDoLogin",
        type: "post",
        dataType: "json",
        data: $.param({
            Info: vk,
            PK: logpk,
            Account: $(Account).val(),
            Password: $(Password).val()
        }), success: function (x) {
            if (x.Status != 1) {
                alert(x.Data);
            }
        }
    });
}

function makeid() {
    var text = "";
    var possible = "ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789";

    for (var i = 0; i < 5; i++)
        text += possible.charAt(Math.floor(Math.random() * possible.length));

    return text;
}
function GK() {
    $.ajax({
        url: SESSIONID + "/Member/GK",
        type: "post",
        dataType: "json",
        success: function (x) {
        }
    });
}
