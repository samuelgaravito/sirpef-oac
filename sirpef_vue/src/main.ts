import app from '@/plugins/app'
import '@/plugins'
import '@/assets/css/app.css'
import '@/assets/css/animations.css'

import { FontAwesomeIcon } from '@fortawesome/vue-fontawesome'

/* import the fontawesome core */
import { library } from '@fortawesome/fontawesome-svg-core'

/* import specific icons */
import { fas, faB, faR} from '@fortawesome/free-solid-svg-icons'

library.add(fas)
library.add(faR)
library.add(faB)
app.component("font-awesome-icon", FontAwesomeIcon)

import { FormWizard, TabContent } from 'vue-form-wizard';

app.component('FormWizard', FormWizard);
app.component('TabContent', TabContent);

app.mount('#app')

