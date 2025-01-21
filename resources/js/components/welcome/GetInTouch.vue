<script setup lang="ts">
import { h } from 'vue';
import validator from 'validator';
import { useForm } from 'vee-validate';
import { toTypedSchema } from '@vee-validate/zod';
import * as z from 'zod';
import axios from 'axios';
import { RiMailSendLine as MailIcon } from 'vue-remix-icons';

import Button from '../Button.vue';
import { FormControl, FormField, FormItem, FormLabel, FormMessage } from '@/components/ui/form';
import {
  Dialog,
  DialogContent,
  DialogDescription,
  DialogClose,
  DialogHeader,
  DialogTitle,
  DialogTrigger,
} from '@/components/ui/dialog';
import { Input } from '@/components/ui/input';
import { toast } from '@/components/ui/toast';

const formSchema = toTypedSchema(
  z.object({
    name: z.string({ required_error: 'Nome obrigatório' }).min(5).max(50),
    email: z
      .string({ required_error: 'Email obrigatório' })
      .refine(validator.isEmail, { message: 'Email inválido' }),
    phone: z
      .string({ required_error: 'Número de telefone obrigatório' })
      .refine(validator.isMobilePhone, { message: 'Número de telefone inválido' }),
    body: z
      .string({ required_error: 'Menssagem obrigatória' })
      .min(10, { message: 'Menssagem muito curta, seja mais eloquente' })
      .max(100, { message: 'Menssagem muito longa, seja mais conciso' }),
  }),
);

const { handleSubmit, resetForm } = useForm({
  validationSchema: formSchema,
});

const onSubmit = handleSubmit((values) => {
  axios
    .post(route('contact'), {
      body: values.body,
      name: values.name,
      email: values.email,
      phone: values.phone,
    })
    .then((response) => {
      resetForm();
    })
    .catch((error) => {
      console.error(error.response.data);
    });

  toast({
    title: 'You submitted the following values:',
    description: h(
      'pre',
      { class: 'mt-2 w-[340px] rounded-md bg-slate-950 p-4' },
      h('code', { class: 'text-white' }, JSON.stringify(values, null, 2)),
    ),
  });
});
</script>

<template>
  <Dialog>
    <DialogTrigger as-child class="fixed bottom-4 z-20">
      <Button>
        <i class="icon text-xl">
          <MailIcon></MailIcon>
        </i>
        Fale Conosco
      </Button>
    </DialogTrigger>

    <DialogContent class="container flex [&_label]:text-slate-700 [&>*]:flex-1 flex-col gap-4">
      <DialogHeader>
        <DialogTitle>Fale Conosco</DialogTitle>
        <DialogDescription> Preencha o formulário para enviar-nos uma menságem. </DialogDescription>
      </DialogHeader>
      <form ref="form" class="w-2/3 space-y-6" @submit="onSubmit">
        <FormField v-slot="{ componentField }" name="name">
          <FormItem v-auto-animate>
            <FormLabel>Nome</FormLabel>
            <FormControl>
              <Input
                class="focus-visible:ring-slate-500"
                type="text"
                placeholder="Nome completo"
                v-bind="componentField"
              />
            </FormControl>
            <FormMessage />
          </FormItem>
        </FormField>
        <FormField v-slot="{ componentField }" name="email">
          <FormItem v-auto-animate>
            <FormLabel>E-Mail</FormLabel>
            <FormControl>
              <Input
                class="focus-visible:ring-slate-500"
                type="text"
                placeholder="e-mail válido"
                v-bind="componentField"
              />
            </FormControl>
            <FormMessage />
          </FormItem>
        </FormField>
        <FormField v-slot="{ componentField }" name="phone">
          <FormItem v-auto-animate>
            <FormLabel>Telefone</FormLabel>
            <FormControl>
              <Input
                v-mask="['(##) ####-####', '(##) #####-####']"
                class="focus-visible:ring-slate-500"
                type="tel"
                placeholder="Número de telefone"
                v-bind="componentField"
              />
            </FormControl>
            <FormMessage />
          </FormItem>
        </FormField>
        <FormField v-slot="{ componentField }" name="body">
          <FormItem v-auto-animate>
            <FormLabel>Mensságem</FormLabel>
            <FormControl>
              <Input
                class="focus-visible:ring-slate-500"
                type="text"
                placeholder="Digite aqui sua menságem"
                v-bind="componentField"
              />
            </FormControl>
            <FormMessage />
          </FormItem>
        </FormField>
        <DialogClose as-child>
          <Button type="submit"> Submit </Button>
        </DialogClose>
      </form>
    </DialogContent>
  </Dialog>
</template>
