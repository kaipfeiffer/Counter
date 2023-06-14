<script setup>
import { RouterLink } from 'vue-router'
import { storeToRefs } from "pinia";
import { useCounterStore } from "@/stores/counter";

const counterStore = useCounterStore();
const { counters, current, getForm, loading } = storeToRefs(counterStore);

// counterStore.getAll();
</script>

<template>
  <div>|{{ typeof(loading) }}|
    <table v-if="!loading">
      <thead>
        <tr>
          <th>
            Zählername:
          </th>
          <th>
            Zaehlerstand:
          </th>
          <th>
            Neu:
          </th>
        </tr>
      </thead>
      <tbody>
        <tr v-for="counter in getForm">
          <td>
            <RouterLink  :to="{ name: 'counter', params: { id: counter.id}}" >{{ counter.name }}</RouterLink> 
          </td>
          <td>
            {{ counter.reading }}
          </td>
          <td>
            <input type="number" step="0.1" v-model="current[counter.id]" />
          </td>
        </tr>
      </tbody>
    </table>
    <div v-if="loading" class="spinner-border spinner-border-sm">Lade Daten</div>
    <div v-if="counters.error" class="text-danger">Error loading users: {{ counters?.error }}</div>
  </div>
</template>
