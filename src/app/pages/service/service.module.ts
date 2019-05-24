import {NgModule} from '@angular/core';
import {ServiceComponent} from './service.component';
import {RouterModule, Routes} from '@angular/router';
import {AppExtModule} from '../../app.ext.module';

const routes: Routes = [
  {
    path: '',
    component: ServiceComponent
  }
];

@NgModule({
  imports: [
    AppExtModule,
    RouterModule.forChild(routes)
  ],
  declarations: [ServiceComponent]
})
export class ServiceModule {
}
