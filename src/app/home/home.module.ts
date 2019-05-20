import {NgModule} from '@angular/core';
import {RouterModule} from '@angular/router';
import {AppExtModule} from '../app.ext.module';

import {HomeComponent} from './home.page';

@NgModule({
    imports: [
        AppExtModule,
        RouterModule.forChild([
            {
                path: '',
                component: HomeComponent
            }
        ])
    ],
    declarations: [HomeComponent]
})
export class HomePageModule {
}
