import Vue from 'vue'
import VueI18n from 'vue-i18n'
import Cookies from 'js-cookie'
import elementEnLocale from 'element-ui/lib/locale/lang/en' // element-ui lang
import enLocale from './en'


Vue.use(VueI18n)

const messages = {
  en: {
    ...enLocale,
    ...elementEnLocale
  }
}

const i18n = new VueI18n({
  locale: Cookies.get('language') || 'en',
  messages
})

export default i18n
