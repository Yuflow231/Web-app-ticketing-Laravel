/*
Thanks to Victor BRANCHU for the logic used within this file
 */

import Toast from "./toast.js";

// regex to check mail validity
const regexMail = "^[^\\s@]+@[^\\s@]+\\.[^\\s@]+$"


// the role of the 2 next functions is to only show the first error found as a toast
let showToast = true;

function allowToast() {
    showToast = true;
}

function preventToast() {
    showToast = false;
}

/**
 *  This function will reset the errors visual of the form's fields
 * @param fieldList the list of the visual element to reset
 */
export function resetFormState(fieldList) {
    allowToast();
    fieldList.forEach(field => {
        field.classList.remove("error-field");
    })
}


/**
 * This function checks if the field is correctly filled
 * @param fields the input(s) to verify
 * @param errorVisualRef the element that will be highlighted
 * @param conditions the condition to respect
 */
export function checkField(fields, errorVisualRef, conditions) {
    // make sure that 'fields' is a list
    if (!Array.isArray(fields)) {
        fields = [fields];
    }

    // check the conditions one by one
    for (const condition of conditions) {
        // give all the element within fields, "..." regroups everything into one list
        if (condition.predicate(...fields)) {
            errorVisualRef.classList.add("error-field");
            if(showToast)
                Toast(condition.message, "error");
            preventToast();
            return false;
        }
    }
    return true;
}


export function verifyMail(error) {
    return{
        predicate : (input) => !input.value.trim().match(regexMail),
        message : error,
    }
}

export function verifyEmptyness(error) {
    return{
        predicate : (input) => input.value === "",
        message : error,
    }
}

export function verifyLength(error) {
    return{
        predicate : (input) => input.value.length < 10,
        message : error,
    }
}


export function verifyFile(error) {
    return{
        predicate : (input) => input.childElementCount < 1,
        message : error,
    }
}

export function verifyDate(error) {
    return{
        predicate : (input) => {
            // Date Validation
            const selectedDate = new Date(input.value);
            const today = new Date();

            // Set time to 00:00:00 to compare only the calendar date
            today.setHours(0, 0, 0, 0);

            // set predicate's value
            return selectedDate < today;
        },
        message : error,
    }
}

/* ----------------------------------------------------------------
!! this one require 2 inputs, it's an exception be aware of that !!
   ---------------------------------------------------------------- */
export function verifyDateDiff(error) {
    return{
        predicate : (start, end) =>{
            const dateStart = new Date(start.value);
            const dateEnd = new Date(end.value);
            return dateEnd < dateStart;
        },
        message : error,
    }
}

/**
 * Call this function when the form is validated
 * @param message success message to show
 * @param ref link to the next page (optional)
 */

// note, this one should not be called when doing something DB related
export function validateForm(message, ref) {
    Toast(message, "success");

    if(ref) {
        setTimeout(() => {
            location.href = ref;
        }, 1500);
    }
}
