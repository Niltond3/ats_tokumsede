<script setup>
import { RiMenuFill as MenuIcon } from 'vue-remix-icons'
import Button from '@/components/Button.vue'
import { Input } from '@/components/ui/input'
import { Label } from '@/components/ui/label'
import {
    Sheet,
    SheetClose,
    SheetContent,
    SheetDescription,
    SheetFooter,
    SheetHeader,
    SheetTitle,
    SheetTrigger,
} from '@/components/ui/sheet'
</script>

<template>
    <Sheet>
        <SheetTrigger as-child>
            <Button variant="outline" class="outline-none border-none p-0 group bg-transparent transition-all">
                <MenuIcon class="w-6 h-6 text-cyan-700/50 group-hover:text-cyan-700 transition-colors" />
            </Button>
        </SheetTrigger>
        <SheetContent side="left">
            <SheetHeader>
                <SheetTitle>Edit profile</SheetTitle>
                <SheetDescription>
                    Make changes to your profile here. Click save when you're done.
                </SheetDescription>
            </SheetHeader>
            <div class="grid gap-4 py-4">
                <SheetClose as-child>
                    <RouterLink to="/">Dashboard</RouterLink>
                </SheetClose>
                <SheetClose as-child>
                    <RouterLink to="/clientes">Clientes</RouterLink>
                </SheetClose>

            </div>
            <!-- <SheetFooter>
                <SheetClose as-child>
                    <Button type="submit">
                        Save changes
                    </Button>
                </SheetClose>
            </SheetFooter> -->
        </SheetContent>
    </Sheet>
</template>
