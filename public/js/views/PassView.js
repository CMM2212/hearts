import Card from "../components/Card.js";
import {EVENTS} from "../constants/events.js";


/**
 * PassView class handles the display and interaction for passing cards in the game at the start of each round.
 */
export default class PassView {
    /**
     * Creates a new PassView.
     *
     * @param {GameModel} gameModel - The game model.
     */
    constructor(gameModel) {
        this.gameModel = gameModel;

        // Store relevant elements for the player's hand UI and pass button.
        this.playerElement = $('#human');
        this.playerHandElement = this.playerElement.find('.hand');
        this.gameButton = $('#pass-button');
        this.init();
    }

    /**
     * Initialize the PassView by setting up event listeners.
     */
    init() {
        this.initEventListeners();
    }

    /**
     * Initialize event listeners for the pass button.
     */
    initEventListeners() {
        this.gameButton.on('click', () => {
            const cardIds = this.getSelectedCardIds();
            if (cardIds.length !== 3) {
                alert('You must select 3 cards to pass.');
                return false;
            }
            this.hide();
            // Dispatch a custom event to the document containing the cardIds. The game controller listens to this.
            document.dispatchEvent(new CustomEvent(EVENTS.PASS_CARDS, { detail: { cardIds } }));
        });
    }

    /**
     * Updates the player's hand UI with the current game state.
     */
    update(cardHands) {
        this.playerHandElement.empty();
        cardHands.forEach(cardHand => {
            const card = cardHand.card;
            const cardObject = new Card(card, cardHand.id, (card) => this.toggleCard(card));
            this.playerHandElement.append(cardObject.render());
        });
        this.show();
    }

    /**
     * Toggles the selection state of a card in the player's hand.
     * @param {HTMLElement} card - The card element to toggle.
     */
    toggleCard(card) {
        if (card.classList.contains('selected')) {
            card.classList.remove('selected');
        } else if (this.getSelectedCardIds().length < 3) {
            card.classList.add('selected');
        }
    }

    /**
     * Show the pass button.
     */
    show() {
        this.gameButton.show();
    }

    /**
     * Hide the pass button.
     */
    hide() {
        this.gameButton.hide();
    }

    /**
     * Get the IDs of the selected cards in the player's hand.
     * @returns {Array} - An array of selected card IDs.
     */
    getSelectedCardIds() {
        return this.playerHandElement.find('.card.selected').map(function() {
            return this.id;
        }).get();
    }
}
