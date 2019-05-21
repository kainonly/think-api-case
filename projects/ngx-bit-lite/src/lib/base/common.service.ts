import {Inject, Injectable, PLATFORM_ID} from '@angular/core';
import {isPlatformBrowser} from '@angular/common';

@Injectable()
export class CommonService {
  constructor(@Inject(PLATFORM_ID) private platformId) {
  }

  redirect(url: string) {
    if (isPlatformBrowser(this.platformId)) {
      location.href = url + '?target_url=' + encodeURIComponent(location.href);
    }
  }
}
