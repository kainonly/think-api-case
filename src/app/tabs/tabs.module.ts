import {NgModule} from '@angular/core';
import {RouterModule} from '@angular/router';
import {AppExtModule} from '../app.ext.module';

import {TabsComponent} from './tabs.component';

@NgModule({
    imports: [
        AppExtModule,
        RouterModule.forChild([
            {
                path: '',
                component: TabsComponent
            }
        ])
    ],
    declarations: [TabsComponent]
})
export class TabsModule {
}
