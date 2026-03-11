/**
 * This function look into the local storage and return the language  found
 * default to english if none are found
 * @returns {string} the language selected
 */
export function getLanguage() {
    let lang = localStorage.getItem("language");

    // if none found set it do the default value, being english
    if (!lang) {
        lang = "en";
        localStorage.setItem("language", lang);
    }

    return lang;
}

/**
 * Set the language inside the local storage
 * @param lang the language to switch to
 */
export function setLanguage(lang) {
    localStorage.setItem("language", lang);
}