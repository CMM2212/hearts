/**
 * Handles login UI for user interaction.
 */
export default class LoginView {
    constructor() {
        this.loginPopup = $('#login-popup');
        this.loginButton = $('#login-button');
        this.loginInput = $('#login-input');
        this.init();
    }

    /**
     * Initializes the login view by setting up event listeners.
     */
    init() {
        this.initEventListeners();
    }

    /**
     * Initializes event listeners for login interactions.
     * - Clicking the login button triggers login interaction.
     * - Pressing 'Enter' in the login input field triggers login interaction.
     */
    initEventListeners() {
        this.loginButton.on('click', () => this.loginInteraction());
        this.loginInput.on('keypress', (event) => {
            if (event.key === 'Enter')
                this.loginInteraction();
        })
    }

    /**
     * Handles login interaction by validating input and dispatching a login event.
     */
    loginInteraction() {
            let name = this.loginInput.val();
            // Trim whitespace from the beginning and end of the string
            name = name.replace(/^\s+|\s+$/g, '');
            if (name.length === 0) {
                alert("Please enter a username");
                return;
            }
            // Dispatch a custom event to the document containing the username.
            // The login controller listens to this.
            document.dispatchEvent(new CustomEvent('login', { detail: name }));
    }

    /**
     * Destroys the login view by removing the login popup from the DOM.
     */
    destroy() {
        this.loginPopup.remove();
    }
}
