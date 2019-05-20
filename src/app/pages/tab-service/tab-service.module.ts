import {NgModule} from '@angular/core';
import {TabServiceComponent} from './tab-service.component';
import {RouterModule, Routes} from '@angular/router';
import {AppExtModule} from '../../app.ext.module';

const routes: Routes = [
  {
    path: '',
    component: TabServiceComponent
  }
];

@NgModule({
  imports: [
    AppExtModule,
    RouterModule.forChild(routes)
  ],
  declarations: [TabServiceComponent]
})
export class TabServiceModule {
}
