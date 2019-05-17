import {ModuleWithProviders, NgModule} from '@angular/core';
import {HttpClientModule} from '@angular/common/http';
import {ConfigService} from './base/config.service';
import {BitService} from './base/bit.service';
import {EventsService} from './base/events.service';
import {HttpService} from './base/http.service';

@NgModule({
  imports: [HttpClientModule]
})
export class NgxBitModule {
  static forRoot(config: any): ModuleWithProviders<NgxBitModule> {
    return {
      ngModule: NgxBitModule,
      providers: [
        BitService,
        HttpService,
        EventsService,
        {provide: ConfigService, useValue: config},
      ],
    };
  }
}
