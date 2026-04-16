import { defineStore } from 'pinia'

export const UseWelcome = defineStore('Welcome', {
    state: () => ({
      title: "",
      subtitle: "",
      img: ""
    }),
    actions: {
      ChangueWelcome(_title: string, _subtitle: string, _img?: string) {
        this.title = _title
        this.subtitle = _subtitle
        this.img = _img
      },
    }
  });
