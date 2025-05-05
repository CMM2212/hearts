/**
 * UserModel class for handling user login.
 */
export default class UserModel {
    /**
     * Logs in a user with the given username.
     * @param {string} username - The username of the user.
     * @returns {Promise} - Response from the server containing userId and success status.
     */
    login(username) {
        return $.ajax(
            {
                url: "api/player/login",
                type: "POST",
                data: {
                    username: username
                },
                dataType: "json"
            }
        );
    }
}
