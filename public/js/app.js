import GameModel from "./models/GameModel.js";
import PassView from "./views/PassView.js";
import GameController from "./controllers/GameController.js";
import TrickView from "./views/TrickView.js";
import ComputerHandView from "./views/ComputerHandView.js";
import DiscardView from "./views/DiscardView.js";
import ScoreboardView from "./views/ScoreboardView.js";
import LoginController from "./controllers/LoginController.js";
import UserModel from "./models/UserModel.js";
import LoginView from "./views/LoginView.js";
import EventQueueController from "./controllers/EventQueueController.js";

/**
 * Front-end application entry point for Hearts game.
 */
class App {
    /**
     * Initializes the application and start the login process.
     */
    constructor() {
        this.userModel = new UserModel();
        this.loginView = new LoginView();
        this.loginController = new LoginController(this.userModel, this.loginView, (playerId) => this.init(playerId));
    }

    /**
     * Initializes the game with the given player ID.
     *
     * @param playerId - The ID of the player from the login process.
     */
    init(playerId) {
        this.gameModel = new GameModel(playerId);
        this.passView = new PassView(this.gameModel);
        this.trickView = new TrickView(this.gameModel);
        this.discardView = new DiscardView(this.gameModel);
        this.computerHandView = new ComputerHandView(this.gameModel);
        this.scoreboardView = new ScoreboardView(this.gameModel);
        this.eventQueueController = new EventQueueController();
        this.gameController = new GameController(this.gameModel, this.passView,
            this.trickView, this.discardView, this.computerHandView, this.scoreboardView, this.eventQueueController);

    }
}

const app = new App();
