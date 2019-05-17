import {Injectable} from '@angular/core';
import {Observable} from 'rxjs';
import {HttpService} from 'ngx-bit-lite';

@Injectable()
export class MainService {
  constructor(private http: HttpService) {
  }

  /**
   * Token验证
   */
  check(): Observable<any> {
    return this.http.req('/check', {});
  }
}
