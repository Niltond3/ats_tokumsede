<template>
  <section class="space-y-4">
    <!-- Nome -->
    <FormField v-slot="{ componentField }" name="nome">
      <FormItem>
        <FormControl>
          <Input v-bind="componentField" type="text" placeholder="Nome do distribuidor" />
        </FormControl>
        <FormLabel>Nome</FormLabel>
        <FormMessage />
      </FormItem>
    </FormField>

    <!-- CNPJ -->
    <FormField v-slot="{ componentField }" name="cnpj">
      <FormItem>
        <FormControl>
          <Input v-bind="componentField" type="text" placeholder="CNPJ" />
        </FormControl>
        <FormLabel>CNPJ</FormLabel>
        <FormMessage />
      </FormItem>
    </FormField>

    <!-- Email -->
    <FormField v-slot="{ componentField }" name="email">
      <FormItem>
        <FormControl>
          <Input v-bind="componentField" type="email" placeholder="Email" />
        </FormControl>
        <FormLabel>Email</FormLabel>
        <FormMessage />
      </FormItem>
    </FormField>

    <!-- Telefone (com máscara e sem campo DDD separado) -->
    <FormField v-slot="{ componentField }" name="telefonePrincipal">
      <FormItem>
        <FormControl>
          <Input
            v-mask="['(##) ####-####', '(##) #####-####']"
            v-bind="componentField"
            type="text"
            placeholder="Telefone"
          />
        </FormControl>
        <FormLabel>Telefone</FormLabel>
        <FormMessage />
      </FormItem>
    </FormField>

    <!-- Outros Contatos -->
    <FormField v-slot="{ componentField }" name="outrosContatos">
      <FormItem>
        <FormControl>
          <Input v-bind="componentField" type="text" placeholder="Outros contatos" />
        </FormControl>
        <FormLabel>Outros Contatos</FormLabel>
        <FormMessage />
      </FormItem>
    </FormField>
  </section>
</template>

<script setup>
import { FormField, FormItem, FormControl, FormLabel, FormMessage } from '@/components/ui/form';
import { Input } from '@/components/ui/input';

const props = defineProps({
  values: { type: Object, required: true },
});
</script>
