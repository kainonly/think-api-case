import {NgModule} from '@angular/core';
import {ScenesComponent} from './scenes.component';
import {RouterModule, Routes} from '@angular/router';
import {AppExtModule} from '../../app.ext.module';

const routes: Routes = [
  {
    path: '',
    component: ScenesComponent
  }
];

@NgModule({
  imports: [
    AppExtModule,
    RouterModule.forChild(routes)
  ],
  declarations: [ScenesComponent]
})
export class ScenesModule {
}
