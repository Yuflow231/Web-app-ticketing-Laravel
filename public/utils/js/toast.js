/*
  !!! DISCLAIMER !!!
  This code is a modification of Adam Argyle code on GitHub
  It has been adjusted to behave as I wanted and commented to prove that I know how it works.
  link to gitHub repo : https://github.com/argyleink/gui-challenges/tree/main/toast
 */


/*
 * Create the section where the toast would be placed.
 * This section will be placed before the body beacon.
 * @returns {HTMLElement} the section where all the toast will be placed
 */
const init = () => {
  const node = document.createElement('section');
  node.classList.add('toast-group');

  document.firstElementChild.insertBefore(node, document.body);
  return node;
}

/*
 * Handle the creation of the toast html wise
 * Its type is set to neutral by default (blue)
 * @param text message wanted
 * @returns {HTMLOutputElement} the toast element
 */
const createToast = (text, type = "neutral") => {
  const node = document.createElement('output');
  
  node.innerText = text;
  node.classList.add('toast');
  node.setAttribute('role', 'status');
  node.setAttribute('aria-live', 'polite');

  node.dataset.type = type;

  return node;
}

/*
 * Add to the Toaster a new toast at the top as its first children
 * @param toast the toast to add
 */
const addToast = toast => {
  Toaster.prepend(toast) // = appendChild at the start of the "list" of toast
}

/*
 * Handled the whole toast behavior
 * from creation to deletion
 */
const Toast = (text, type) => {
  // Create a new toast based on text
  let toast = createToast(text, type)
  addToast(toast)

  // handle the life cycle of the toast
  return new Promise(async (resolve) => {

    // wait the toast animations to finish
    await Promise.allSettled(
      toast.getAnimations().map(animation => 
        animation.finished
      )
    )
    Toaster.removeChild(toast)

    // clear the Promise, mark it as finished
    resolve() 
  })
}

// Initialise the section receiving the toasts
const Toaster = init();

// Allow import from other file as name Toast
export default Toast;