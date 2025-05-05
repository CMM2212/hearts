import {EVENTS} from "../constants/events.js";

/**
 * Responsible for connecting the login button to the login API call.
 */
export default class LoginController {

    /**
     * Creates a new login controller.
     * @param {UserModel} userModel - Model that handles the API call.
     * @param {LoginView} loginView - View that handles the UI.
     * @param {Function} callback - Function to be called after login (App.init(userId))
     */
    constructor(userModel, loginView, callback) {
        this.userModel = userModel;
        this.loginView = loginView;
        this.callback = callback;
        this.init();
    }

    /**
     * Initialize the login controller by setting up event listeners.
     */
    init() {
        this.initEventListeners();
    }

    /**
     * Add event listener for the login event from the login view UI button.
     * This will be triggered when the user clicks the login button.
     */
    initEventListeners() {
        document.addEventListener(EVENTS.LOGIN, (event) => this.login(event["detail"]));
    }

    /**
     * Handles the login event by calling the login API and handling the response.
     * @param {string} username - The username from the login view's input field.
     */
    login(username) {
        // Call the login API and handle the response.
        this.userModel.login(username)
            .done(response => {
                if (response.success) {
                    console.log("Logged in", response.userId);
                    this.loginView.destroy(); // Remove login popup
                    this.callback(response.userId); // Call the callback with the userId to initialize the game
                }
            }).fail(error => console.error(error));
    }

}
