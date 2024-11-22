<script setup>
import { computed, h, ref } from 'vue'
import { useWindowSize } from '@vueuse/core';
import GuestLayout from '@/Layouts/GuestLayout.vue';
import { Head } from '@inertiajs/vue3';
import { CalendarDate, getLocalTimeZone, parseDate, today } from '@internationalized/date'
import {
    RiLoginBoxLine as LoginIcon,
    RiGenderlessLine as GenderlessIcon,
    RiCalendarLine as CalendarIcon
} from "vue-remix-icons";
import { CheckIcon, CircleIcon, DotIcon } from '@radix-icons/vue'
import { format, parseISO } from 'date-fns';
import ptBR from 'date-fns/locale/pt-BR';
import validator from 'validator'
import { useForm } from 'vee-validate'
import { toTypedSchema } from '@vee-validate/zod'
import * as z from 'zod'
import { vAutoAnimate } from '@formkit/auto-animate/vue'
import { cn } from "@/lib/utils";
import axios from 'axios';

import { Stepper, StepperDescription, StepperItem, StepperSeparator, StepperTitle, StepperTrigger } from '@/components/ui/stepper'
import { Select, SelectContent, SelectGroup, SelectItem, SelectTrigger, SelectValue } from '@/components/ui/select'
import { Form, FormControl, FormField, FormItem, FormLabel, FormMessage } from '@/components/ui/form'
import { Popover, PopoverContent, PopoverTrigger } from '@/components/ui/popover'
import { RadioGroup, RadioGroupItem } from '@/components/ui/radio-group'
import { Input } from '@/components/ui/input'
import Button from '@/components/Button.vue';
import DatePicker from '../components/datePicker.vue';
import { Calendar } from '@/components/ui/calendar'


defineProps({
    canResetPassword: {
        type: Boolean,
    },
    status: {
        type: String,
    },
});

const { width } = useWindowSize()

const getSexo = {
    mobile: {
        1: 'Masculino',
        2: 'Feminino',
    },
    desktop: {
        1: 'M',
        2: 'F',
    }
}

const formSchema = toTypedSchema([
    z.object({
        nome: z.string({ required_error: 'Informar seu nome é obrigatório' }),
        telefone: z.string({ required_error: 'Número de telefone obrigatório' }).refine(validator.isMobilePhone, { message: 'Número de telefone inválido' }),
        senha: z.string({ required_error: 'Senha obrigatória' }).min(4),
        confirmSenha: z.string({ required_error: 'Confirme sua senha' }).min(4),
        sexo: z.enum(['1', '2']).nullable().optional(),
        dataNascimento: z.string().nullable().optional(),
        tipoPessoa: z.string().nullable().optional(),
        email: z.string({ required_error: "e-mail obrigatório" }),
    }).refine(
        ({ confirmSenha, senha }) => senha === confirmSenha,
        {
            message: 'Passwords must match!',
            path: ['confirmPassword'],
        },
    ),
    z.object({
        password: z.string().min(2).max(50),
        confirmPassword: z.string(),
    })
    ,
    z.object({
        favoriteDrink: z.union([z.literal('coffee'), z.literal('tea'), z.literal('soda')]),
    }),
]);

const stepIndex = ref(1)

const steps = [
    {
        step: 1,
        title: 'Your details',
        description: 'Provide your name and email',
    },
    {
        step: 2,
        title: 'Your password',
        description: 'Choose a password',
    },
    {
        step: 3,
        title: 'Your Favorite Drink',
        description: 'Choose a drink',
    },
]

const { values } = useForm({
    validationSchema: formSchema,
    initialValues: {
        //
    }
})



const getDataFormat = (date) => {
    const formatMask = width > 639 ? "dd'º de' MMM',' yyyy" : 'dd/MM/yyyy'
    const dateToIso = (date) => parseISO(date.toString());
    return format(dateToIso(date), formatMask, { locale: ptBR })
}

const value = computed({
    get: () => values.dataNascimento ? parseDate(values.dataNascimento) : undefined,
    set: val => val,
})

const getTipoPessoaPayload = (documentValue) => {

    if (documentValue.length <= 14) {
        return {
            tipoPessoa: '1',
            documento: {
                'CPF': documentValue,
                'CNPJ': null

            }
        }
    }

    return {
        tipoPessoa: '2',
        documento: {
            'CPF': null,
            'CNPJ': documentValue
        }
    }

}

const onSubmit = (values) => {
    const phoneRaw = values.telefone.replace(/\D/g, '')

    const ddd = phoneRaw.slice(0, 2);
    const telefone = phoneRaw.slice(2);

    const { tipoPessoa, documento } = getTipoPessoaPayload(values.tipoPessoa);

    const payload = {
        ddd,
        telefone,
        senha: values.senha,
        sexo: values.sexo,
        dataNascimento: values.dataNascimento,
        email: values.email,
        tipoPessoa,
        cpf: documento['CPF'],
        cnpj: documento['CNPJ'],
    }
    console.log(payload)

    // axios.post(route('cliente.register'), payload)
    //     .then((response) => {
    //         console.log('response')
    //         console.log(response)
    //         resetField('senha')
    //         location.reload();
    //     }).catch((error) => {
    //         console.error(error);
    //         resetField('senha')
    //     })

}

</script>

<template>
    <GuestLayout>

        <Head title="Registre-se" />

        <div v-if="status" class="mb-4 font-medium text-sm text-green-600">
            {{ status }}
        </div>

        <Form v-slot="{ meta, values, validate, setFieldValue }" as="" keep-values
            :validation-schema="toTypedSchema(formSchema[stepIndex - 1])">
            <Stepper v-slot="{ isNextDisabled, isPrevDisabled, nextStep, prevStep }" v-model="stepIndex"
                class="block w-full">
                <form @submit="(e) => {
                    e.preventDefault()
                    validate()

                    if (stepIndex === steps.length && meta.valid) {
                        onSubmit(values)
                    }
                }">


                    <div class="flex flex-col gap-4 mt-4">
                        <template v-if="stepIndex === 1">
                            <div class="space-y-6 sm:grid sm:grid-cols-6 sm:gap-4 sm:space-y-0">
                                <FormField v-slot="{ componentField }" name="nome">
                                    <FormItem v-auto-animate class="sm:col-span-5">
                                        <FormLabel>Nome</FormLabel>
                                        <FormControl>
                                            <Input class="focus-visible:ring-slate-500" type="tel"
                                                placeholder="Nome completo" v-bind="componentField"
                                                autocomplete="name" />
                                        </FormControl>
                                        <FormMessage />
                                    </FormItem>
                                </FormField>
                                <FormField v-slot="{ componentField }" name="sexo">
                                    <FormItem v-auto-animate class="sm:col-span-1">
                                        <FormLabel> <span class="sm:hidden">selecione seu </span>sexo</FormLabel>
                                        <Popover>
                                            <PopoverTrigger as-child>
                                                <FormControl>
                                                    <Button variant="outline"
                                                        class="relative text-slate-500 text-sm !p-2 min-h-[22px] !rounded-sm font-normal flex justify-start !px-3">
                                                        <span v-if="values.sexo === undefined" class="icon text-xl">
                                                            <GenderlessIcon />
                                                        </span>
                                                        <span v-if="values.sexo != undefined && width > 639">{{
                                                            getSexo.desktop[values.sexo]
                                                        }}</span>
                                                        <span v-if="values.sexo != undefined && width < 640">{{
                                                            getSexo.mobile[values.sexo]
                                                        }}</span>
                                                    </Button>
                                                </FormControl>
                                            </PopoverTrigger>
                                            <PopoverContent class="w-80">
                                                <FormControl>
                                                    <RadioGroup v-bind="componentField">
                                                        <FormItem class="flex items-center space-x-2">
                                                            <FormControl>
                                                                <RadioGroupItem value="1" />
                                                            </FormControl>
                                                            <FormLabel>Másculino</FormLabel>
                                                        </FormItem>
                                                        <FormItem class="flex items-center space-x-2">
                                                            <FormControl>
                                                                <RadioGroupItem value="2" />
                                                            </FormControl>
                                                            <FormLabel>Feminino</FormLabel>
                                                        </FormItem>
                                                    </RadioGroup>
                                                </FormControl>
                                            </PopoverContent>
                                        </Popover>
                                        <FormMessage />
                                    </FormItem>
                                </FormField>
                                <FormField v-slot="{ componentField }" name="telefone">
                                    <FormItem v-auto-animate class="sm:col-span-3">
                                        <FormLabel>Telefone</FormLabel>
                                        <FormControl>
                                            <Input class="focus-visible:ring-slate-500" type="tel"
                                                v-mask="['(##) ####-####', '(##) #####-####']"
                                                placeholder="Número de telefone" v-bind="componentField"
                                                autocomplete="username" />
                                        </FormControl>
                                        <FormMessage />
                                    </FormItem>
                                </FormField>
                                <FormField v-slot="{ componentField }" name="tipoPessoa">
                                    <FormItem v-auto-animate class="sm:col-span-3">
                                        <FormLabel>CPF/CNPJ</FormLabel>
                                        <FormControl>
                                            <Input class="focus-visible:ring-slate-500" type="text"
                                                v-mask="['###.###.###-##', '##.###.###/####-##']"
                                                placeholder="Número de telefone" v-bind="componentField" />
                                        </FormControl>
                                        <FormMessage />
                                    </FormItem>
                                </FormField>
                                <FormField v-slot="{ componentField }" name="email">
                                    <FormItem v-auto-animate class="sm:col-span-4">
                                        <FormLabel>E-mail</FormLabel>
                                        <FormControl>
                                            <Input class="focus-visible:ring-slate-500" type="email"
                                                placeholder="E-mail válido" v-bind="componentField"
                                                autocomplete="username" />
                                        </FormControl>
                                        <FormMessage />
                                    </FormItem>
                                </FormField>
                                <FormField name="dataNascimento">
                                    <FormItem class="flex flex-col sm:col-span-2">
                                        <FormLabel>Data de Nascimento</FormLabel>
                                        <Popover>
                                            <PopoverTrigger as-child>
                                                <FormControl>
                                                    <Button variant="outline" :class="cn(
                                                        'text-slate-500 text-sm !p-2 min-h-[22px] !rounded-sm font-normal flex justify-start !px-3',
                                                        !computed({
                                                            get: () => values.dataNascimento ? parseDate(values.dataNascimento) : undefined,
                                                            set: val => val,
                                                        }) && 'text-muted-foreground',
                                                    )">
                                                        <span>
                                                            {{ values.dataNascimento &&
                                                                getDataFormat(values.dataNascimento)
                                                            }}
                                                        </span>
                                                        <span v-if="width < 640">Selecione uma data</span>
                                                        <CalendarIcon class="ms-auto h-4 w-4 opacity-50" />
                                                    </Button>
                                                </FormControl>
                                            </PopoverTrigger>
                                            <PopoverContent class="p-0">
                                                <DatePicker v-model="value" calendar-label="Date of birth" initial-focus
                                                    :min-value="new CalendarDate(1900, 1, 1)"
                                                    :max-value="today(getLocalTimeZone())" @update:model-value="(v) => {
                                                        if (v) {
                                                            setFieldValue('dataNascimento', v.toString())
                                                        }
                                                        else {
                                                            setFieldValue('dataNascimento', undefined)
                                                        }
                                                    }" />
                                                <!-- <DatePicker :value="computed({
                                                    get: () => values.dataNascimento ? parseDate(values.dataNascimento) : undefined,
                                                    set: val => val,
                                                })" calendar-label="Data de Nascimento" initial-focus
                                                    :min-value="new CalendarDate(1900, 1, 1)"
                                                    :max-value="today(getLocalTimeZone())" @update:model-value="(v) => {
                                                        if (v) setFieldValue('dataNascimento', v.toString())
                                                        else setFieldValue('dataNascimento', undefined)
                                                    }" /> -->
                                            </PopoverContent>
                                        </Popover>
                                        <FormMessage />
                                    </FormItem>
                                </FormField>
                                <FormField v-slot="{ componentField }" name="senha">
                                    <FormItem v-auto-animate class="sm:col-span-3">
                                        <FormLabel>Senha</FormLabel>
                                        <FormControl>
                                            <Input type="password" placeholder="Senha" v-bind="componentField"
                                                autocomplete="new-password" />
                                        </FormControl>
                                        <FormMessage />
                                    </FormItem>
                                </FormField>
                                <FormField v-slot="{ componentField }" name="confirmSenha">
                                    <FormItem v-auto-animate class="sm:col-span-3">
                                        <FormLabel>Confirmação de senha</FormLabel>
                                        <FormControl>
                                            <Input type="password" placeholder="Confirme sua senha"
                                                v-bind="componentField" autocomplete="new-password" />
                                        </FormControl>
                                        <FormMessage />
                                    </FormItem>
                                </FormField>
                            </div>
                        </template>

                        <template v-if="stepIndex === 2">
                            <FormField v-slot="{ componentField }" name="password">
                                <FormItem>
                                    <FormLabel>Password</FormLabel>
                                    <FormControl>
                                        <Input type="password" v-bind="componentField" />
                                    </FormControl>
                                    <FormMessage />
                                </FormItem>
                            </FormField>

                            <FormField v-slot="{ componentField }" name="confirmPassword">
                                <FormItem>
                                    <FormLabel>Confirm Password</FormLabel>
                                    <FormControl>
                                        <Input type="password" v-bind="componentField" />
                                    </FormControl>
                                    <FormMessage />
                                </FormItem>
                            </FormField>
                        </template>

                        <template v-if="stepIndex === 3">
                            <FormField v-slot="{ componentField }" name="favoriteDrink">
                                <FormItem>
                                    <FormLabel>Drink</FormLabel>

                                    <Select v-bind="componentField">
                                        <FormControl>
                                            <SelectTrigger>
                                                <SelectValue placeholder="Select a drink" />
                                            </SelectTrigger>
                                        </FormControl>
                                        <SelectContent>
                                            <SelectGroup>
                                                <SelectItem value="coffee">
                                                    Coffe
                                                </SelectItem>
                                                <SelectItem value="tea">
                                                    Tea
                                                </SelectItem>
                                                <SelectItem value="soda">
                                                    Soda
                                                </SelectItem>
                                            </SelectGroup>
                                        </SelectContent>
                                    </Select>
                                    <FormMessage />
                                </FormItem>
                            </FormField>
                        </template>
                    </div>

                    <div class="flex items-center justify-between mt-4">
                        <Button :disabled="isPrevDisabled" variant="outline" size="sm" @click="prevStep()">
                            Back
                        </Button>
                        <div class="flex items-center gap-3">
                            <Button v-if="stepIndex !== 3" :type="meta.valid ? 'button' : 'submit'"
                                :disabled="isNextDisabled" size="sm" @click="meta.valid && nextStep()">
                                Next
                            </Button>
                            <Button v-if="stepIndex === 3" size="sm" type="submit">
                                Submit
                            </Button>
                        </div>
                    </div>
                </form>
            </Stepper>
        </Form>
    </GuestLayout>
</template>

<!--
<form class="space-y-6 sm:grid sm:grid-cols-6 sm:gap-4 sm:space-y-0" @submit="onSubmit">

    <div class="flex  gap-4p sm:col-end-7 sm:col-span-3 sm:justify-end">
        <Button type="submit" class="icon text-xl" :class="{ 'opacity-25': isSubmitting }"
            :disabled="isSubmitting">
            <LoginIcon />
            Cadastrar
        </Button>
    </div>
</form> -->
