<template>
  <section class="space-y-4">
    <h3 class="text-lg font-semibold">Horários</h3>
    <!-- Domingo: Ativo -->
    <FormField v-slot="{ componentField }" name="novo_domingo">
      <FormItem>
        <FormControl>
          <Input v-bind="componentField" type="number" placeholder="Domingo (0 ou 1)" />
        </FormControl>
        <FormLabel>Domingo (Ativo: 1, Inativo: 0)</FormLabel>
        <FormMessage />
      </FormItem>
    </FormField>
    <!-- Início de Domingo -->
    <FormField v-slot="{ componentField }" name="novo_inicioDomingo">
      <FormItem>
        <FormControl>
          <Input v-bind="componentField" type="time" placeholder="Início de Domingo" />
        </FormControl>
        <FormLabel>Início de Domingo</FormLabel>
        <FormMessage />
      </FormItem>
    </FormField>
    <!-- Fim de Domingo -->
    <FormField v-slot="{ componentField }" name="novo_fimDomingo">
      <FormItem>
        <FormControl>
          <Input v-bind="componentField" type="time" placeholder="Fim de Domingo" />
        </FormControl>
        <FormLabel>Fim de Domingo</FormLabel>
        <FormMessage />
      </FormItem>
    </FormField>

    <h3 class="text-lg font-semibold">Horários - Sábado</h3>
    <!-- Sábado: Ativo -->
    <FormField v-slot="{ componentField }" name="novo_sabado">
      <FormItem>
        <FormControl>
          <Input v-bind="componentField" type="number" placeholder="Sábado (0 ou 1)" />
        </FormControl>
        <FormLabel>Sábado (Ativo: 1, Inativo: 0)</FormLabel>
        <FormMessage />
      </FormItem>
    </FormField>
    <!-- Início de Sábado -->
    <FormField v-slot="{ componentField }" name="novo_inicioSabado">
      <FormItem>
        <FormControl>
          <Input v-bind="componentField" type="time" placeholder="Início de Sábado" />
        </FormControl>
        <FormLabel>Início de Sábado</FormLabel>
        <FormMessage />
      </FormItem>
    </FormField>
    <!-- Fim de Sábado -->
    <FormField v-slot="{ componentField }" name="novo_fimSabado">
      <FormItem>
        <FormControl>
          <Input v-bind="componentField" type="time" placeholder="Fim de Sábado" />
        </FormControl>
        <FormLabel>Fim de Sábado</FormLabel>
        <FormMessage />
      </FormItem>
    </FormField>

    <h3 class="text-lg font-semibold">Taxas e Valores</h3>
    <!-- Taxa Única -->
    <FormField v-slot="{ componentField }" name="taxaUnica">
      <FormItem>
        <FormControl>
          <Input v-bind="componentField" type="number" placeholder="Taxa Única" />
        </FormControl>
        <FormLabel>Taxa Única</FormLabel>
        <FormMessage />
      </FormItem>
    </FormField>
    <!-- Valor da Taxa Única -->
    <FormField v-slot="{ componentField }" name="valorTaxaUnica">
      <FormItem>
        <FormControl>
          <Input v-bind="componentField" type="number" placeholder="Valor da Taxa Única" />
        </FormControl>
        <FormLabel>Valor da Taxa Única</FormLabel>
        <FormMessage />
      </FormItem>
    </FormField>
    <!-- Taxa Domingo -->
    <FormField v-slot="{ componentField }" name="taxaDomingo">
      <FormItem>
        <FormControl>
          <Input v-bind="componentField" type="number" placeholder="Taxa Domingo" />
        </FormControl>
        <FormLabel>Taxa Domingo</FormLabel>
        <FormMessage />
      </FormItem>
    </FormField>
    <!-- Valor da Taxa Domingo -->
    <FormField v-slot="{ componentField }" name="valorTaxaDomingo">
      <FormItem>
        <FormControl>
          <Input v-bind="componentField" type="number" placeholder="Valor da Taxa Domingo" />
        </FormControl>
        <FormLabel>Valor da Taxa Domingo</FormLabel>
        <FormMessage />
      </FormItem>
    </FormField>
    <!-- Taxa Compra Mínima -->
    <FormField v-slot="{ componentField }" name="taxaCompraMinima">
      <FormItem>
        <FormControl>
          <Input v-bind="componentField" type="number" placeholder="Taxa Compra Mínima" />
        </FormControl>
        <FormLabel>Taxa Compra Mínima</FormLabel>
        <FormMessage />
      </FormItem>
    </FormField>
    <!-- Valor da Compra Mínima -->
    <FormField v-slot="{ componentField }" name="valorCompraMinima">
      <FormItem>
        <FormControl>
          <Input v-bind="componentField" type="number" placeholder="Valor da Compra Mínima" />
        </FormControl>
        <FormLabel>Valor da Compra Mínima</FormLabel>
        <FormMessage />
      </FormItem>
    </FormField>
    <!-- Taxa de Entrega Distante -->
    <FormField v-slot="{ componentField }" name="taxaEntregaDistante">
      <FormItem>
        <FormControl>
          <Input v-bind="componentField" type="number" placeholder="Taxa de Entrega Distante" />
        </FormControl>
        <FormLabel>Taxa de Entrega Distante</FormLabel>
        <FormMessage />
      </FormItem>
    </FormField>
    <!-- Distância Máxima -->
    <FormField v-slot="{ componentField }" name="distanciaMaxima">
      <FormItem>
        <FormControl>
          <Input v-bind="componentField" type="number" placeholder="Distância Máxima" />
        </FormControl>
        <FormLabel>Distância Máxima</FormLabel>
        <FormMessage />
      </FormItem>
    </FormField>
    <!-- Valor por Km Adicional -->
    <FormField v-slot="{ componentField }" name="valorKmAdicional">
      <FormItem>
        <FormControl>
          <Input v-bind="componentField" type="number" placeholder="Valor por Km Adicional" />
        </FormControl>
        <FormLabel>Valor por Km Adicional</FormLabel>
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
