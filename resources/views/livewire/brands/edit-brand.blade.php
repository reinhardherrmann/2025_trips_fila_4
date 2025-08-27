<div>

    <form wire:submit="save" class="mt-6">
        {{ $this->form }}

        <div class="mt-5">
            <flux:button variant="primary" color="red"
                         class=""
                         wire:click="cancel"
            >Abbruch

            </flux:button>

            <flux:button variant="primary" color="green"
                         class="ms-3"
                         type="submit">Speichern
            </flux:button>
        </div>

    </form>

    <x-filament-actions::modals />
</div>
