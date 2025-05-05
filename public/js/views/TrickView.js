import Card from "../components/Card.js";
import {EVENTS} from "../constants/events.js";

/**
 * Handles displaying the cards being played in the current trick.
 */
export default class TrickView {
    constructor() {
        this.playerElement = $('#human');
        this.playerHandElement = this.playerElement.find('.hand');
        // this.playerDiscardElement = $('#discard-bottom');
        // this.init();
    }
    //
    // init() {
    //     this.initEventListeners();
    // }
    //
    // initEventListeners() {
    // }


    /**
     * Updates the trick view by creating a card object for each card in the trick and appending it to the trick view.
     * @param data
     */
    update(data) {
        this.playerHandElement.empty();
        data = Object.values(data);
        data.forEach(cardHand => {
            const card = cardHand.card;
            let cardObject;
            if (cardHand.isPlayable)
                cardObject = new Card(card, cardHand.id, (card) => this.playCard(card));
            else
                cardObject = new Card(card, cardHand.id);
            this.playerHandElement.append(cardObject.render());
        });
    }

    /**
     * Removes card from player's hand and sends event to play card.
     * @param card
     */
    playCard(card) {
        for (const cardElement of this.playerHandElement.children()) {
            if (cardElement.id === card.id) {
                cardElement.remove();
                break;
            }
        }
        document.dispatchEvent(new CustomEvent(EVENTS.DISCARD_CARD, { detail: card }));
        document.dispatchEvent(new CustomEvent(EVENTS.PLAY_CARD, { detail: card.id }));

    }

    show() {
        // this.section.show();
    }

    hide() {
        // this.section.hide();
    }
}
