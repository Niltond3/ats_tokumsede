<script setup>
import { ref, onMounted } from 'vue';
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
import {
  IconPhone,
  IconLock,
  IconSun,
  IconMoon,
  IconEye,
  IconEyeOff,
  IconLoader2,
  IconLogin2,
} from '@tabler/icons-vue';
import { Separator } from '@/components/ui/separator';
import renderToast from '@/components/renderPromiseToast';
import { login } from '@/services/api/clientAuth';
import SocialLogin from '@/components/SocialLogin.vue';

const { theme, toggleTheme } = useTheme();
const showPassword = ref(false);

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

const { handleSubmit, isSubmitting, resetForm } = useForm({
  validationSchema: formSchema,
  initialValues: {
    remember: false,
    telefone: '',
    senha: '',
  },
});

const onSubmit = handleSubmit((values) => {
  const phoneRaw = values.telefone.replace(/\D/g, '');
  const credentials = {
    ddd: phoneRaw.slice(0, 2),
    telefone: phoneRaw.slice(2),
    senha: values.senha,
    remember: values.remember,
  };

  renderToast(
    login(credentials),
    'Realizando Login...',
    'Login realizado com sucesso',
    'Falha ao realizar login',
    () => {
      location.reload();
    },
    (err) => {
      if (err === 'Network Error') location.reload();
    },
  ).finally(() => {
    resetForm();
    // window.location.href = route('cliente.dashboard');
  });
});
const handleSuccessLogin = (response) => {
  location.reload();
};
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
          <!-- <Button variant="ghost" size="icon" @click="toggleTheme">
            <IconSun v-if="theme === 'dark'" class="h-5 w-5 transition-all" />
            <IconMoon v-else class="h-5 w-5 transition-all" />
          </Button> -->
        </div>
        <CardDescription class="text-muted-foreground">
          Entre com suas credenciais ou use login social
        </CardDescription>
      </CardHeader>

      <CardContent>
        <SocialLogin @login:success="handleSuccessLogin"></SocialLogin>
        <Separator class="my-4" />
        <!-- Enhanced Form -->
        <form class="space-y-4 animate-in fade-in-50" @submit="onSubmit">
          <FormField v-slot="{ componentField, errorMessage }" name="telefone">
            <FormItem>
              <FormLabel class="bg-white rounded-md z-10">Telefone</FormLabel>
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
              <FormLabel class="bg-white rounded-md z-10">Senha</FormLabel>
              <FormControl>
                <div class="relative">
                  <IconLock class="absolute left-3 top-2.5 h-5 w-5 text-muted-foreground" />
                  <Input
                    v-bind="componentField"
                    :type="showPassword ? 'text' : 'password'"
                    placeholder="Digite sua senha"
                    class="px-10"
                  />
                  <Button
                    type="button"
                    variant="ghost"
                    size="icon"
                    class="absolute right-1 px-0 top-1/2 -translate-y-1/2 text-info/70 transition-colors rounded-md !bg-transparent hover:text-info"
                    @click="showPassword = !showPassword"
                  >
                    <i v-if="!showPassword" class="ri-eye-fill"></i>
                    <i v-else class="ri-eye-off-fill"></i>
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
            <IconLogin2 v-else class="mr-2" />
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
