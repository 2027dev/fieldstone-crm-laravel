import Alpine from 'alpinejs';
import collapse from '@alpinejs/collapse';

window.Alpine = Alpine;
Alpine.plugin(collapse);

/**
 * Drag-and-drop for the deal pipeline board. Cards post their new stage to the
 * server and are optimistically moved in the DOM.
 */
Alpine.data('pipelineBoard', (moveUrlTemplate) => ({
    draggingId: null,
    overStage: null,

    start(event, dealId) {
        this.draggingId = dealId;
        event.dataTransfer.effectAllowed = 'move';
        event.dataTransfer.setData('text/plain', String(dealId));
    },

    end() {
        this.draggingId = null;
        this.overStage = null;
    },

    async drop(event, stageId) {
        event.preventDefault();

        const dealId = this.draggingId ?? event.dataTransfer.getData('text/plain');
        this.overStage = null;

        if (!dealId) {
            return;
        }

        const card = document.getElementById(`deal-card-${dealId}`);
        const target = document.getElementById(`stage-drop-${stageId}`);

        if (card && target && card.dataset.stage !== String(stageId)) {
            target.appendChild(card);
            card.dataset.stage = String(stageId);

            await fetch(moveUrlTemplate.replace('__ID__', dealId), {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    Accept: 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                },
                body: JSON.stringify({ stage_id: stageId }),
            });

            window.location.reload();
        }

        this.draggingId = null;
    },
}));

Alpine.start();
