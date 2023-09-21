<template>
  <span class="setPageSpan tableNavigationSpan">
    <a href="javascript:void(0)" @click="$emit('setPage', 0)">
      {{ settings.firstPage }}
    </a>
  </span>
  <span class="setPageSpan tableNavigationSpan">
    <a href="javascript:void(0)" @click="$emit('setPage', -1)">
      {{ settings.previousPage }}
    </a>
  </span>
  <span class="titleSpan tableNavigationSpan">
    {{ getTitle }}
  </span>
  <span class="shortTitleSpan tableNavigationSpan">
    {{ getShortTitle }}
  </span>
  <span class="setPageSpan tableNavigationSpan">
    <a href="javascript:void(0)" @click="$emit('setPage', 1)">
      {{ settings.nextPage }}
    </a>
  </span>
  <span class="setPageSpan tableNavigationSpan">
    <a href="javascript:void(0)" @click="$emit('setPage', null)">
      {{ settings.lastPage }}
    </a>
  </span>
</template>
<script>
import { computed, defineComponent } from "vue";

export default defineComponent({

  emits: ['setPage'],
  name: 'TableNavigation',
  props: {
    settings: { type: Object },
    start: { required: true, type: String },
    end: { required: true, type: String },
    total: { required: true, type: String },
  },
  setup: (props) => {
    const defaultSettings = {
      pageSize: 20,
      title: "Einträge %1$s-%2$s von %3$s",
      shortTitle: "%1$s-%2$s/%3$s",
      firstPage: "<<",
      previousPage: "<",
      nextPage: ">",
      lastPage: ">>",
    }

    const getShortTitle = computed(() => {
      let title = settings.shortTitle
        .replace('%3$s', props.total)
        .replace('%1$s', props.start)
        .replace('%2$s', props.end)
      return title
    })

    const getTitle = computed(() => {
      let title = settings.title
        .replace('%3$s', props.total)
        .replace('%1$s', props.start)
        .replace('%2$s', props.end)
      return title
    })

    const settings = { ...defaultSettings, ...props.settings }

    return { getShortTitle, getTitle, settings }
  },
  watch: {
  }
});
</script>
<style scoped>
.tableNavigationSpan {
  display: inline-block;
  padding: 5px 10px;
  text-align: center;
}
.tableNavigationSpan a {
  padding: 5px 10px;
}

.tableNavigationSpan.setPageSpan {
  width: 10%;
  min-width: 1em;
}

.tableNavigationSpan.titleSpan,
.tableNavigationSpan.shortTitleSpan {
  width: auto;
}

.tableNavigationSpan.titleSpan {
  display: none;
}


@media (min-width: 376px) {
  .tableNavigationSpan.titleSpan {
    display: inline-block;
  }

  .tableNavigationSpan.shortTitleSpan {
    display: none;
  }
}
</style>
