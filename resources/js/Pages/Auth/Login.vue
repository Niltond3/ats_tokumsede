<script setup>
import { onMounted, ref } from 'vue';
import GuestLayout from '@/Layouts/GuestLayout.vue';
import { Head } from '@inertiajs/vue3';
import { useForm } from 'vee-validate';
import * as z from 'zod';
import { vAutoAnimate } from '@formkit/auto-animate/vue';
import axios from 'axios';
import { FormControl, FormField, FormItem, FormLabel, FormMessage } from '@/components/ui/form';
import { Input } from '@/components/ui/input';
import Button from '@/components/Button.vue';
import { toTypedSchema } from '@vee-validate/zod';
import { useTheme } from '@/composables/useTheme';
import {
  Card,
  CardHeader,
  CardTitle,
  CardDescription,
  CardContent,
  CardFooter,
} from '@/components/ui/card';
import { Switch } from '@/components/ui/switch';
import {
  IconPhone,
  IconLock,
  IconEye,
  IconEyeOff,
  IconLogin2,
  IconLoader2,
} from '@tabler/icons-vue';
import { Separator } from '@/components/ui/separator';
import renderToast from '@/components/renderPromiseToast';

const { theme, toggleTheme } = useTheme();
const showPassword = ref(false);

// Add social login providers

defineProps({
  canResetPassword: {
    type: Boolean,
  },
  status: {
    type: String,
  },
});

const formSchema = toTypedSchema(
  z.object({
    login: z.string({ required_error: 'Login é obrigatório' }),
    senha: z.string({ required_error: 'Senha obrigatória' }),
    remember: z.boolean().default(false).optional(),
  }),
);

const { handleSubmit, isSubmitting } = useForm({
  validationSchema: formSchema,
  initialValues: {
    remember: false,
  },
});

const onSubmit = handleSubmit((values, { resetField }) => {
  renderToast(
    axios.post(route('login'), values),
    'Realizando login...',
    'Login realizado com sucesso!',
    'Erro ao realizar login',
    () => {
      resetField('senha');
      location.reload();
    },
    () => {
      resetField('senha');
      location.reload();
    },
  );
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
          <!-- <Button variant="ghost" class="p-0" size="icon" @click="toggleTheme">
            <IconSun
              v-if="theme === 'dark'"
              class="h-5 w-5 transition-all"
              :size="48"
              stroke-width="1"
            />
            <IconMoon v-else class="h-5 w-5 transition-all" :size="48" stroke-width="1" />
          </Button> -->
        </div>
        <CardDescription class="text-muted-foreground">
          Entre com suas credenciais administrativas
        </CardDescription>
      </CardHeader>

      <div v-if="status" class="mb-4 font-medium text-sm text-green-600">
        {{ status }}
      </div>

      <CardContent>
        <Separator class="my-4" />
        <form class="space-y-5 animate-in fade-in-50" @submit="onSubmit">
          <FormField v-slot="{ componentField, errorMessage }" name="login">
            <FormItem v-auto-animate>
              <FormLabel class="z-10">Login</FormLabel>
              <FormControl>
                <div class="relative">
                  <IconPhone
                    class="absolute left-3 top-2.5 h-5 w-5 text-muted-foreground"
                    :size="48"
                    stroke-width="1"
                  />
                  <Input
                    class="focus-visible:ring-slate-500 pl-10"
                    type="text"
                    placeholder="Login"
                    v-bind="componentField"
                    autocomplete="username"
                  />
                </div>
              </FormControl>
              <FormMessage>{{ errorMessage }}</FormMessage>
            </FormItem>
          </FormField>
          <FormField v-slot="{ componentField, errorMessage }" name="senha">
            <FormItem>
              <FormLabel class="z-10">Senha</FormLabel>
              <FormControl>
                <div class="relative">
                  <IconLock
                    class="absolute left-3 top-2.5 h-5 w-5 text-muted-foreground"
                    :size="48"
                    stroke-width="1"
                  />
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
                    class="absolute right-2 top-1/2 -translate-y-1/2 p-0"
                    @click="showPassword = !showPassword"
                  >
                    <IconEye v-if="!showPassword" class="h-5 w-5" :size="48" stroke-width="1" />
                    <IconEyeOff v-else class="h-5 w-5 p-0" :size="48" stroke-width="1" />
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
          </div>
          <Button
            type="submit"
            class="w-full transition-all"
            :class="{ 'opacity-50': isSubmitting }"
            :disabled="isSubmitting"
          >
            <IconLoader2
              v-if="isSubmitting"
              class="mr-2 h-4 w-4 animate-spin"
              :size="48"
              stroke-width="1"
            />
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
