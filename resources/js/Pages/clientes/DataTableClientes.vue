<script setup>
</script>

<template>
<div class="card w-100">
            <div class="card-body p-2">
                <div class="table-responsive">
                    <DialogClients :open="isOpen" :toggleDialog="toggleDialog" :idClienteAddress="idClienteAddress" />
                    <table id="datatable-clientes"
                        class="!text-gray-700 display w-full max-w-full mb-4 bg-transparent border-collapse table-striped dt-responsive nowrap table-sm table-bordered v-middle table-bordered nowrap"
                        cellspacing="0" width="100%">
                        <thead style="background: #1E88E5; color: #F3F9FD;">
                            <tr>
                                <th data-priority="2"></th>
                                <th data-priority="3">Nome</th>
                                <th>CPF/CNPJ</th>
                                <th>Telefone</th>
                                <th>Outros Contatos</th>
                                <th data-priority="4">Rating</th>
                                <th data-priority="1">Opções</th>
                            </tr>
                        </thead>
                    </table>
                </div>
            </div>
        </div>
</template>
