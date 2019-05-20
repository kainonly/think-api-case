import {NgModule} from '@angular/core';
import {CommonModule} from '@angular/common';
import {FormsModule, ReactiveFormsModule} from '@angular/forms';
import {NgZorroAntdMobileModule} from 'ng-zorro-antd-mobile';
import {IonicModule} from '@ionic/angular';

@NgModule({
    exports: [
        CommonModule,
        FormsModule,
        ReactiveFormsModule,
        NgZorroAntdMobileModule,
        IonicModule,
    ]
})
export class AppExtModule {
}
