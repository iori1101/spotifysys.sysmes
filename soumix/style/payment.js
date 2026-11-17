const cardNumber = document.getElementById("cardNumber");
const cardIcon = document.getElementById("cardIcon");

const expiry = document.getElementById("expiry");
const cvv = document.getElementById("cvv");
const cardName = document.getElementById("cardName");

const form = document.getElementById("paymentForm");

const cardError = document.getElementById("cardError");
const expiryError = document.getElementById("expiryError");
const cvvError = document.getElementById("cvvError");
const nameError = document.getElementById("nameError");

function getCardType(number) {
    if (/^4/.test(number)) return "visa";
    if (/^(5[1-5]|2[2-7])/.test(number)) return "mastercard";
    if (/^(34|37)/.test(number)) return "amex";
    return "";
}

cardNumber.addEventListener("input", function () {
    let number = cardNumber.value.replace(/\D/g, "");
    const type = getCardType(number);

    cardIcon.className = "card-icon";

    if (type) {
        cardIcon.classList.add(type);
        cardIcon.textContent = "";
    } else {
        cardIcon.textContent = "💳";
    }

    if (type === "amex") {
        number = number.substring(0, 15);
        number = number.replace(
            /(\d{4})(\d{1,6})?(\d{1,5})?/,
            (_, a, b, c) => [a, b, c].filter(Boolean).join(" ")
        );
    } else {
        number = number.substring(0, 16);
        number = number.replace(/(\d{4})(?=\d)/g, "$1 ");
    }

    cardNumber.value = number;
});

function isValidCardNumber(number) {
    number = number.replace(/\D/g, "");

    if (number.length < 13 || number.length > 19) {
        return false;
    }

    let sum = 0;
    let doubleNumber = false;

    for (let i = number.length - 1; i >= 0; i--) {
        let digit = Number(number[i]);

        if (doubleNumber) {
            digit *= 2;

            if (digit > 9) {
                digit -= 9;
            }
        }

        sum += digit;
        doubleNumber = !doubleNumber;
    }

    return sum % 10 === 0;
}

expiry.addEventListener("input", function () {
    let value = expiry.value.replace(/\D/g, "").substring(0, 4);

    if (value.length >= 3) {
        value = value.substring(0, 2) + " / " + value.substring(2);
    }

    expiry.value = value;
});

cvv.addEventListener("input", function () {
    cvv.value = cvv.value.replace(/\D/g, "");

    const type = getCardType(
        cardNumber.value.replace(/\D/g, "")
    );

    cvv.value = cvv.value.substring(
        0,
        type === "amex" ? 4 : 3
    );
});


/* =========================
   SUBMIT
   ========================= */

form.addEventListener("submit", function (event) {

    // IMPORTANT:
    // Stop the normal submit initially so we can validate.
    event.preventDefault();

    let valid = true;

    cardError.textContent = "";
    expiryError.textContent = "";
    cvvError.textContent = "";
    nameError.textContent = "";

    /* CARD */

    if (!isValidCardNumber(cardNumber.value)) {
        cardError.textContent = "Please enter a valid card number.";
        valid = false;
    }

    /* EXPIRY */

    const expiryValue = expiry.value.replace(/\D/g, "");

    if (expiryValue.length !== 4) {

        expiryError.textContent =
            "Enter a valid expiration date.";

        valid = false;

    } else {

        const month = Number(expiryValue.substring(0, 2));
        const year = Number("20" + expiryValue.substring(2));

        const now = new Date();
        const currentYear = now.getFullYear();
        const currentMonth = now.getMonth() + 1;

        if (
            month < 1 ||
            month > 12 ||
            year < currentYear ||
            (year === currentYear && month < currentMonth)
        ) {
            expiryError.textContent =
                "Your card appears to be expired.";

            valid = false;
        }
    }

    /* CVV */

    const cardType = getCardType(
        cardNumber.value.replace(/\D/g, "")
    );

    const requiredCVV = cardType === "amex" ? 4 : 3;

    if (cvv.value.length !== requiredCVV) {

        cvvError.textContent =
            `CVV must contain ${requiredCVV} digits.`;

        valid = false;
    }

    /* NAME */

    if (cardName.value.trim().length < 2) {

        nameError.textContent =
            "Please enter the cardholder name.";

        valid = false;
    }


    /* =========================
       VALID
       ========================= */

    if (valid) {

        /*
         * At this point validation passed.
         *
         * Use your payment provider here to create
         * a payment token/payment-method ID.
         */

        const paymentToken = "...";

        // Send ONLY the token to your PHP endpoint.
        const tokenInput = document.createElement("input");

        tokenInput.type = "hidden";
        tokenInput.name = "payment_token";
        tokenInput.value = paymentToken;

        form.appendChild(tokenInput);

        // Now the normal form submission happens.
        form.submit();
    }
});
