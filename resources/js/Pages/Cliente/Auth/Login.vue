<script setup>
import { ref } from 'vue';
import GuestLayout from '@/Layouts/GuestLayout.vue';
import { Head } from '@inertiajs/vue3';
import { useForm } from 'vee-validate';
import { toTypedSchema } from '@vee-validate/zod';
import * as z from 'zod';
import validator from 'validator';
import axios from 'axios';
import { useTheme } from '@/composables/useTheme';
import { Button } from '@/components/ui/button';
import {
  Card,
  CardHeader,
  CardTitle,
  CardDescription,
  CardContent,
  CardFooter,
} from '@/components/ui/card';
import {
  Form,
  FormControl,
  FormField,
  FormItem,
  FormLabel,
  FormMessage,
} from '@/components/ui/form';
import { Input } from '@/components/ui/input';
import { Switch } from '@/components/ui/switch';
import { IconPhone, IconLock, IconSun, IconMoon, IconEye, IconEyeOff } from '@tabler/icons-vue';
import { Separator } from '@/components/ui/separator';
import renderToast from '@/components/renderPromiseToast';

const { theme, toggleTheme } = useTheme();
const showPassword = ref(false);

// Add social login providers
const socialProviders = [
  {
    name: 'Google',
    icon: 'IconBrandGoogle',
    color: 'bg-red-500',
  },
  {
    name: 'GitHub',
    icon: 'IconBrandGithub',
    color: 'bg-gray-900',
  },
];

const formSchema = toTypedSchema(
  z.object({
    telefone: z
      .string()
      .min(1, 'Telefone é obrigatório')
      .refine(validator.isMobilePhone, 'Telefone inválido'),
    senha: z.string().min('Senha deve ter no mínimo 6 caracteres'),
    remember: z.boolean().default(false).optional(),
  }),
);

const { handleSubmit, isSubmitting } = useForm({
  validationSchema: formSchema,
  initialValues: {
    remember: false,
    telefone: '',
    senha: '',
  },
  validateOnMount: true,
});

const onSubmit = handleSubmit((values, { resetField }) => {
  console.log('Starting form submission');
  //   const values = form.values;
  console.log('Form submitted with values:', values);
  const phoneRaw = values.telefone.replace(/\D/g, '');
  const payload = {
    ddd: phoneRaw.slice(0, 2),
    telefone: phoneRaw.slice(2),
    senha: values.senha,
    remember: values.remember,
  };

  axios.defaults.baseURL = import.meta.env.VITE_APP_URL || 'http://127.0.0.1:8000';
  axios.defaults.withCredentials = true;

  renderToast(
    axios
      .post('/cliente/login', payload, {
        headers: {
          'X-Requested-With': 'XMLHttpRequest',
          Accept: 'application/json',
          'Content-Type': 'application/json',
        },
      })
      .then(async (response) => {
        console.log(response);
        if (response.status === 200) {
          window.location.href = route('cliente.dashboard');
        }
        return response;
      }),
    'Realizando Login...',
    'Login realizado com sucesso',
    'Falha ao realizar login',
    (resp) => {
      console.log(resp);
    },
    (err) => {
      console.log(err);
    },
  ).finally(() => {
    console.log('Form submission completed');
    // window.location.href = route('cliente.dashboard');
  });
});
</script>

<template>
  <GuestLayout>
    <Head title="Login" />

    <Card class="w-full max-w-md mx-auto">
      <CardHeader class="space-y-4">
        <div class="flex justify-between items-center">
          <CardTitle
            class="text-2xl font-bold bg-gradient-to-r from-primary to-primary-foreground bg-clip-text text-transparent"
          >
            Bem-vindo
          </CardTitle>
          <Button variant="ghost" size="icon" @click="toggleTheme">
            <IconSun v-if="theme === 'dark'" class="h-5 w-5 transition-all" />
            <IconMoon v-else class="h-5 w-5 transition-all" />
          </Button>
        </div>
        <CardDescription class="text-muted-foreground">
          Entre com suas credenciais ou use login social
        </CardDescription>
      </CardHeader>

      <CardContent>
        <!-- Social Login -->

        <div class="grid grid-cols-2 gap-4 mb-6">
          <Button
            v-for="provider in socialProviders"
            :key="provider.name"
            variant="outline"
            class="w-full"
          >
            <component :is="provider.icon" class="mr-2 h-4 w-4" />
            {{ provider.name }}
          </Button>
        </div>
        <Separator class="my-4" />
        <!-- Enhanced Form -->

        <form class="space-y-4 animate-in fade-in-50" @submit="onSubmit">
          <FormField v-slot="{ componentField, errorMessage }" name="telefone">
            <FormItem>
              <FormLabel>Telefone</FormLabel>
              <FormControl>
                <div class="relative">
                  <IconPhone class="absolute left-3 top-2.5 h-5 w-5 text-muted-foreground" />
                  <Input
                    v-mask="['(##) ####-####', '(##) #####-####']"
                    v-bind="componentField"
                    type="tel"
                    placeholder="(00) 00000-0000"
                    class="pl-10"
                  />
                </div>
              </FormControl>
              <FormMessage>{{ errorMessage }}</FormMessage>
            </FormItem>
          </FormField>

          <FormField v-slot="{ componentField, errorMessage }" name="senha">
            <FormItem>
              <FormLabel>Senha</FormLabel>
              <FormControl>
                <div class="relative">
                  <IconLock class="absolute left-3 top-2.5 h-5 w-5 text-muted-foreground" />
                  <Input
                    v-bind="componentField"
                    :type="showPassword ? 'text' : 'password'"
                    placeholder="Digite sua senha"
                    class="pl-10"
                  />
                  <Button
                    type="button"
                    variant="ghost"
                    size="icon"
                    class="absolute right-2 top-2"
                    @click="showPassword = !showPassword"
                  >
                    <IconEye v-if="!showPassword" class="h-5 w-5" />
                    <IconEyeOff v-else class="h-5 w-5" />
                  </Button>
                </div>
              </FormControl>
              <FormMessage>{{ errorMessage }}</FormMessage>
            </FormItem>
          </FormField>

          <div class="flex items-center justify-between">
            <FormField v-slot="{ value, handleChange }" name="remember">
              <FormItem class="flex items-center space-x-2">
                <FormControl>
                  <Switch :checked="value" class="z-10" @update:checked="handleChange" />
                </FormControl>
                <FormLabel class="!m-0 whitespace-nowrap -top-5">Lembrar-me</FormLabel>
              </FormItem>
            </FormField>

            <Button variant="link" :href="route('password.request')"> Esqueceu a senha? </Button>
          </div>

          <Button
            type="submit"
            class="w-full transition-all"
            :class="{ 'opacity-50': isSubmitting }"
            :disabled="isSubmitting"
          >
            <IconLoader2 v-if="isSubmitting" class="mr-2 h-4 w-4 animate-spin" />
            {{ isSubmitting ? 'Processando...' : 'Entrar' }}
          </Button>
        </form>
      </CardContent>

      <CardFooter class="flex flex-col space-y-4 sm:flex-row sm:justify-between sm:space-y-0">
        <Button variant="link" :href="route('password.request')" class="text-sm">
          Esqueceu a senha?
        </Button>
        <Button variant="link" :href="route('cliente.register')" class="text-sm">
          Criar nova conta
        </Button>
      </CardFooter>
    </Card>
  </GuestLayout>
</template>
