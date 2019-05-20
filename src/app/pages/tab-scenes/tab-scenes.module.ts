import {NgModule} from '@angular/core';
import {TabScenesComponent} from './tab-scenes.component';
import {RouterModule, Routes} from '@angular/router';
import {AppExtModule} from '../../app.ext.module';

const routes: Routes = [
  {
    path: '',
    component: TabScenesComponent
  }
];

@NgModule({
  imports: [
    AppExtModule,
    RouterModule.forChild(routes)
  ],
  declarations: [TabScenesComponent]
})
export class TabScenesModule {
}
