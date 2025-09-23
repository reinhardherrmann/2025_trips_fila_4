<div>
    <form wire:submit="save">
        {{ $this->form }}

        <div class="mt-5">
            <flux:button
                variant="primary"
                color="red"
                wire:click="mountAction('cancel')"
            >
                Abbruch
            </flux:button>

            <flux:button variant="primary" color="green" class="ms-3" type="submit">
                Speichern
            </flux:button>
        </div>
    </form>

    <x-filament-actions::modals/>
</div>
