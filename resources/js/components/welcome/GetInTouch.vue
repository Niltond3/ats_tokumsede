<script setup lang="ts">
import { h } from 'vue'
import validator from 'validator';
import { useForm } from 'vee-validate'
import { toTypedSchema } from '@vee-validate/zod'
import * as z from 'zod'

import { Button } from '@/components/ui/button'
import {
  FormControl,
  FormDescription,
  FormField,
  FormItem,
  FormLabel,
  FormMessage
} from '@/components/ui/form'
import { Input } from '@/components/ui/input'
import { toast } from '@/components/ui/toast'


const formSchema = toTypedSchema(z.object({
  name: z.string().min(2).max(50),
  email: z.string().refine(validator.isEmail),
  phone: z.string().refine(validator.isMobilePhone),
  messager: z.string().min(10).max(100)
}))

const { isFieldDirty, handleSubmit } = useForm({
  validationSchema: formSchema,
})

const onSubmit = handleSubmit((values) => {
    toast({
    title: 'You submitted the following values:',
    description: h('pre', { class: 'mt-2 w-[340px] rounded-md bg-slate-950 p-4' }, h('code', { class: 'text-white' }, JSON.stringify(values, null, 2))),
  })
})
</script>

<template>
    <section class="container flex [&_*]:text-slate-700 [&>*]:flex-1 flex-col gap-4 xl:px-64 mt-20 py-10" id="faleconosco">
        <h2 class="my-8">Fale Conosco</h2>
  <form class="w-2/3 space-y-6" @submit="onSubmit">
    <FormField v-slot="{ componentField }" name="name" :validate-on-blur="!isFieldDirty">
      <FormItem v-auto-animate>
        <FormLabel>Nome</FormLabel>
        <FormControl>
          <Input type="text" placeholder="Nome completo" v-bind="componentField" />
        </FormControl>
        <FormMessage />
      </FormItem>
    </FormField>
    <FormField v-slot="{ componentField }" name="email" :validate-on-blur="!isFieldDirty">
      <FormItem v-auto-animate>
        <FormLabel>E-Mail</FormLabel>
        <FormControl>
          <Input type="text" placeholder="e-mail válido" v-bind="componentField" />
        </FormControl>
        <FormMessage />
      </FormItem>
    </FormField>
    <FormField v-slot="{ componentField }" name="phone" :validate-on-blur="!isFieldDirty">
      <FormItem v-auto-animate>
        <FormLabel>Telefone</FormLabel>
        <FormControl>
          <Input type="text" placeholder="Número de telefone" v-bind="componentField" />
        </FormControl>
        <FormMessage />
      </FormItem>
    </FormField>
    <FormField v-slot="{ componentField }" name="Messager" :validate-on-blur="!isFieldDirty">
      <FormItem v-auto-animate>
        <FormLabel>Mensságem</FormLabel>
        <FormControl>
          <Input type="text" placeholder="Digite aqui sua menságem" v-bind="componentField" />
        </FormControl>
        <FormMessage />
      </FormItem>
    </FormField>
    <Button type="submit">
      Submit
    </Button>
  </form>
    </section>

</template>
