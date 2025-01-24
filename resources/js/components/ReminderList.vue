<script setup>
import { onMounted } from 'vue';
import { useReminders } from '@/composables/useReminders';
import { useReminderActions } from '@/composables/useReminderActions';
import { Card, CardContent } from '@/components/ui/card';
import { Button } from '@/components/ui/button';
import {
  Select,
  SelectContent,
  SelectItem,
  SelectTrigger,
  SelectValue,
} from '@/components/ui/select';

const props = defineProps({
  clientId: {
    type: Number,
    required: true,
  },
});

const emit = defineEmits(['edit']);
const { reminders, isLoading, currentPage, totalPages, activeFilters, fetchReminders } =
  useReminders(props.clientId);

const { deleteReminder } = useReminderActions();

const handleDelete = async (id) => {
  await deleteReminder(id);
  fetchReminders(currentPage.value);
};

const handleStatusChange = (status) => {
  activeFilters.value.status = status;
  fetchReminders(1);
};

onMounted(() => {
  fetchReminders();
});
</script>

<template>
  <div class="space-y-4">
    <Select v-model="activeFilters.status">
      <SelectTrigger>
        <SelectValue placeholder="Filtrar por status" />
      </SelectTrigger>
      <SelectContent>
        <SelectItem value="ATIVO">Ativos</SelectItem>
        <SelectItem value="INATIVO">Inativos</SelectItem>
      </SelectContent>
    </Select>

    <div v-if="isLoading" class="space-y-2">
      <Card v-for="n in 3" :key="n" class="animate-pulse">
        <CardContent class="h-24 bg-muted/50" />
      </Card>
    </div>

    <TransitionGroup v-else name="list" tag="div" class="space-y-2">
      <Card v-for="reminder in reminders" :key="reminder.id" class="transition-all duration-300">
        <CardContent class="p-4 flex justify-between items-start">
          <div>
            <p class="font-medium">{{ reminder.descricao }}</p>
            <p v-if="reminder.data_limite" class="text-sm text-muted-foreground">
              Limite: {{ new Date(reminder.data_limite).toLocaleDateString() }}
            </p>
          </div>
          <div class="flex gap-2">
            <Button variant="ghost" size="icon" @click="emit('edit', reminder)">
              <i class="ri-edit-line" />
            </Button>
            <Button
              variant="ghost"
              size="icon"
              class="text-destructive"
              @click="handleDelete(reminder.id)"
            >
              <i class="ri-delete-bin-line" />
            </Button>
          </div>
        </CardContent>
      </Card>
    </TransitionGroup>

    <div v-if="totalPages > 1" class="flex justify-center gap-2 mt-4">
      <Button
        variant="outline"
        size="sm"
        :disabled="currentPage === 1"
        @click="fetchReminders(currentPage - 1)"
      >
        Anterior
      </Button>
      <Button
        variant="outline"
        size="sm"
        :disabled="currentPage === totalPages"
        @click="fetchReminders(currentPage + 1)"
      >
        Próxima
      </Button>
    </div>
  </div>
</template>

<style scoped>
.list-enter-active,
.list-leave-active {
  transition: all 0.3s ease;
}
.list-enter-from,
.list-leave-to {
  opacity: 0;
  transform: translateX(30px);
}
</style>
