window.onload = function () {
    const name = document.getElementById("fullname");
    const cardnumber = document.getElementById("card_number");
    const expirationdate = document.getElementById("expirationdate");
    const securitycode = document.getElementById("cvv");
    const output = document.getElementById("output");
    const ccicon = document.getElementById("ccicon");
    const ccsingle = document.getElementById("ccsingle");

    let cctype = null;

    var cardnumber_mask = new IMask(cardnumber, {
        mask: [
            {
                mask: "0000 000000 00000",
                regex: "^3[47]\\d{0,13}",
                cardtype: "american express",
            },
            {
                mask: "0000 0000 0000 0000",
                regex: "^(?:6011|65\\d{0,2}|64[4-9]\\d?)\\d{0,12}",
                cardtype: "discover",
            },
            {
                mask: "0000 000000 0000",
                regex: "^3(?:0([0-5]|9)|[689]\\d?)\\d{0,11}",
                cardtype: "diners",
            },
            {
                mask: "0000 0000 0000 0000",
                regex: "^(5[1-5]\\d{0,2}|22[2-9]\\d{0,1}|2[3-7]\\d{0,2})\\d{0,12}",
                cardtype: "mastercard",
            },

            {
                mask: "0000 000000 00000",
                regex: "^(?:2131|1800)\\d{0,11}",
                cardtype: "jcb15",
            },
            {
                mask: "0000 0000 0000 0000",
                regex: "^(?:35\\d{0,2})\\d{0,12}",
                cardtype: "jcb",
            },
            {
                mask: "0000 0000 0000 0000",
                regex: "^(?:5[0678]\\d{0,2}|6304|67\\d{0,2})\\d{0,12}",
                cardtype: "maestro",
            },

            {
                mask: "0000 0000 0000 0000",
                regex: "^4\\d{0,15}",
                cardtype: "visa",
            },
            {
                mask: "0000 0000 0000 0000",
                regex: "^62\\d{0,14}",
                cardtype: "unionpay",
            },
            {
                mask: "0000 0000 0000 0000",
                cardtype: "Unknown",
            },
        ],
        dispatch: function (appended, dynamicMasked) {
            var number = (dynamicMasked.value + appended).replace(/\D/g, "");

            for (var i = 0; i < dynamicMasked.compiledMasks.length; i++) {
                let re = new RegExp(dynamicMasked.compiledMasks[i].regex);
                if (number.match(re) != null) {
                    return dynamicMasked.compiledMasks[i];
                }
            }
        },
    });

    var expirationdate_mask = new IMask(expirationdate, {
        mask: "MM{/}YY",
        blocks: {
            MM: {
                mask: IMask.MaskedRange,
                from: 1,
                to: 12,
            },
            YY: {
                mask: IMask.MaskedRange,
                from: 0,
                to: 99,
            },
        },
    });

    var securitycode_mask = new IMask(securitycode, {
        mask: "0000",
    });

    const swapColor = function (basecolor) {
        document.querySelectorAll(".lightcolor").forEach(function (input) {
            input.setAttribute("class", "");
            input.setAttribute("class", "lightcolor " + basecolor);
        });
        document.querySelectorAll(".darkcolor").forEach(function (input) {
            input.setAttribute("class", "");
            input.setAttribute("class", "darkcolor " + basecolor + "dark");
        });
    };

    cardnumber_mask.on("accept", function () {
        console.log(cardnumber_mask.masked.currentMask.cardtype);
        switch (cardnumber_mask.masked.currentMask.cardtype) {
            case "american express":
                ccicon.innerHTML =
                    '<i class="fab fa-cc-amex" style="color: #2e77bc;"></i>';
                ccsingle.innerHTML =
                    '<i class="fab fa-cc-amex" style="color: #2e77bc;"></i>';
                swapColor("green");
                break;
            case "visa":
                ccicon.innerHTML =
                    '<i class="fab fa-cc-visa" style="color: #1a1f71;"></i>';
                ccsingle.innerHTML =
                    '<i class="fab fa-cc-visa" style="color: #1a1f71;"></i>';
                swapColor("lime");
                break;
            case "diners":
                ccicon.innerHTML =
                    '<i class="fab fa-cc-diners-club" style="color: #0079be;"></i>';
                ccsingle.innerHTML =
                    '<i class="fab fa-cc-diners-club" style="color: #0079be;"></i>';
                swapColor("orange");
                break;
            case "discover":
                ccicon.innerHTML =
                    '<i class="fab fa-cc-discover" style="color: #ff6000;"></i>';
                ccsingle.innerHTML =
                    '<i class="fab fa-cc-discover" style="color: #ff6000;"></i>';
                swapColor("purple");
                break;
            case "jcb":
            case "jcb15":
                ccicon.innerHTML =
                    '<i class="fab fa-cc-jcb" style="color: #0b4ea2;"></i>';
                ccsingle.innerHTML =
                    '<i class="fab fa-cc-jcb" style="color: #0b4ea2;"></i>';
                swapColor("red");
                break;
            case "maestro":
                ccicon.innerHTML =
                    '<i class="fab fa-cc-maestro" style="color: #0099df;"></i>';
                ccsingle.innerHTML =
                    '<i class="fab fa-cc-maestro" style="color: #0099df;"></i>';
                swapColor("yellow");
                break;
            case "mastercard":
                ccicon.innerHTML =
                    '<i class="fab fa-cc-mastercard" style="color: #eb001b;"></i>';
                ccsingle.innerHTML =
                    '<i class="fab fa-cc-mastercard" style="color: #eb001b;"></i>';
                swapColor("lightblue");
                break;
            case "unionpay":
                ccicon.innerHTML =
                    '<i class="fab fa-cc-stripe" style="color: #00447c;"></i>';
                ccsingle.innerHTML =
                    '<i class="fab fa-cc-stripe" style="color: #00447c;"></i>';
                swapColor("cyan");
                break;
            default:
                ccicon.innerHTML =
                    '<i class="far fa-credit-card" style="color: #999999;"></i>';
                ccsingle.innerHTML =
                    '<i class="far fa-credit-card" style="color: #999999;"></i>';
                swapColor("gray");
        }
    });

    document.querySelector(".preload").classList.remove("preload");
    document
        .querySelector(".creditcard")
        .addEventListener("click", function () {
            if (this.classList.contains("flipped")) {
                this.classList.remove("flipped");
            } else {
                this.classList.add("flipped");
            }
        });

    name.addEventListener("input", function () {
        if (name.value.length == 0) {
            document.getElementById("svgname").innerHTML = "John Doe";
            document.getElementById("svgnameback").innerHTML = "John Doe";
        } else {
            document.getElementById("svgname").innerHTML = this.value;
            document.getElementById("svgnameback").innerHTML = this.value;
        }
    });

    cardnumber_mask.on("accept", function () {
        if (cardnumber_mask.value.length == 0) {
            document.getElementById("svgnumber").innerHTML =
                "0123 4567 8910 1112";
        } else {
            document.getElementById("svgnumber").innerHTML =
                cardnumber_mask.value;
        }
    });

    expirationdate_mask.on("accept", function () {
        if (expirationdate_mask.value.length == 0) {
            document.getElementById("svgexpire").innerHTML = "01/23";
        } else {
            document.getElementById("svgexpire").innerHTML =
                expirationdate_mask.value;
        }
    });

    securitycode_mask.on("accept", function () {
        if (securitycode_mask.value.length == 0) {
            document.getElementById("svgsecurity").innerHTML = "985";
        } else {
            document.getElementById("svgsecurity").innerHTML =
                securitycode_mask.value;
        }
    });

    name.addEventListener("focus", function () {
        document.querySelector(".creditcard").classList.remove("flipped");
    });

    cardnumber.addEventListener("focus", function () {
        document.querySelector(".creditcard").classList.remove("flipped");
    });

    expirationdate.addEventListener("focus", function () {
        document.querySelector(".creditcard").classList.remove("flipped");
    });

    securitycode.addEventListener("focus", function () {
        document.querySelector(".creditcard").classList.add("flipped");
    });
};
