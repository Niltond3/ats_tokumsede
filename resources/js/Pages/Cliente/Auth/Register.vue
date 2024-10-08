<script setup>
import { Check, Circle, Dot } from 'lucide-vue-next'
import { toTypedSchema } from '@vee-validate/zod'
import * as z from 'zod'
import { h, ref } from 'vue'
import { useForm } from 'vee-validate'
import GuestLayout from '@/Layouts/GuestLayout.vue';
import { Head } from '@inertiajs/vue3';
import validator from 'validator'
import { Stepper, StepperDescription, StepperItem, StepperSeparator, StepperTitle, StepperTrigger } from '@/components/ui/stepper'
import { Form, FormControl, FormField, FormItem, FormLabel, FormMessage } from '@/components/ui/form'
import {
    Select,
    SelectContent,
    SelectGroup,
    SelectItem,
    SelectTrigger,
    SelectValue,
} from '@/components/ui/select'
import { Input } from '@/components/ui/input'
import { Button } from '@/components/ui/button'
import { toast } from '@/components/ui/toast'
import PersonalRegister from '../components/personalRegister.vue'
import AddressRegister from '../components/addressRegister.vue'
import ConfirmRegister from '../components/confirmRegister.vue'

const { status } = defineProps(['status']);


const formSchema = [
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
        (values) => {
            return values.senha === values.confirmSenha
        },
        {
            message: 'As senhas devem ser',
            path: ['confirmSenha'],
        },
    ),
    z.object({
        password: z.string().min(2).max(50),
        confirmPassword: z.string(),
    }),
    z.object({
        favoriteDrink: z.union([z.literal('coffee'), z.literal('tea'), z.literal('soda')]),
    }),
]

const stepIndex = ref(1)

const steps = [
    {
        step: 1,
        title: 'Informações pessoais e de contato',
        description: 'Mais tarde você vai poder alterar o que quiser',
    },
    {
        step: 2,
        title: 'Informe seu endereço para entrega',
        description: 'Será possível alterar e adicionar novos endereços depois',
    },
    {
        step: 3,
        title: 'Revisão',
        description: 'Veja se está tudo certo',
    },
]

const { handleSubmit, isSubmitting, values, setFieldValue } = useForm({
    validationSchema: formSchema,
    initialValues: {
        //
    }
})

const onSubmit = handleSubmit((values, { resetField }) => {
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

    toast({
        title: 'You submitted the following values:',
        description: h('pre', { class: 'mt-2 w-[340px] rounded-md bg-slate-950 p-4' }, h('code', { class: 'text-white' }, JSON.stringify(values, null, 2))),
    })
})

</script>

<template>
    <GuestLayout>

        <Head title="Registre-se" />

        <div v-if="status" class="mb-4 font-medium text-sm text-green-600">
            {{ status }}
        </div>

        <Form v-slot="{ meta, values, validate }" as="" keep-values
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
                    <div class="flex w-full flex-start gap-2">
                        <StepperItem v-for="step in steps" :key="step.step" v-slot="{ state }"
                            class="relative flex w-full flex-col items-center justify-center" :step="step.step">
                            <StepperSeparator v-if="step.step !== steps[steps.length - 1].step"
                                class="absolute left-[calc(50%+20px)] right-[calc(-50%+10px)] top-5 block h-0.5 shrink-0 rounded-full bg-muted group-data-[state=completed]:bg-primary" />

                            <StepperTrigger as-child>
                                <Button :variant="state === 'completed' || state === 'active' ? 'default' : 'outline'"
                                    size="icon" class="z-10 rounded-full shrink-0"
                                    :class="[state === 'active' && 'ring-2 ring-ring ring-offset-2 ring-offset-background']"
                                    :disabled="state !== 'completed' && !meta.valid">
                                    <Check v-if="state === 'completed'" class="size-5" />
                                    <Circle v-if="state === 'active'" />
                                    <Dot v-if="state === 'inactive'" />
                                </Button>
                            </StepperTrigger>

                            <div class="mt-5 flex flex-col items-center text-center">
                                <StepperTitle :class="[state === 'active' && 'text-primary']"
                                    class="text-sm font-semibold transition lg:text-base">
                                    {{ step.title }}
                                </StepperTitle>
                                <StepperDescription :class="[state === 'active' && 'text-primary']"
                                    class="sr-only text-xs text-muted-foreground transition md:not-sr-only lg:text-sm">
                                    {{ step.description }}
                                </StepperDescription>
                            </div>
                        </StepperItem>
                    </div>

                    <div class="">
                        <PersonalRegister :set-field-value="setFieldValue" :values="values"></PersonalRegister>

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
