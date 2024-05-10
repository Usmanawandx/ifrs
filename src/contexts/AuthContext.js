import React, {
  createContext, useReducer, useMemo,
} from 'react';
import PropTypes from 'prop-types';


export const AuthContext = createContext();

const initialState = Object.freeze({
  isLoggedIn: false,
});

const reducer = (state, action,user={}) => {
  console.log(action)
  switch (action.type) {
    case 'SIGN_IN': {
      return { isLoggedIn: true };
    }
    case 'SIGN_OUT': {
      return { isLoggedIn: false };
    }
    default:
      return initialState;
  }
};

const AuthProvider = ({ children }) => {
  const [auth, dispatch] = useReducer(reducer, initialState);

  const contextValue = useMemo(() => (
    { auth, dispatch }
  ), [auth, dispatch]);

  return (
    <AuthContext.Provider value={contextValue}>
      {children}
    </AuthContext.Provider>
  );
};

AuthProvider.propTypes = {
  children: PropTypes.node.isRequired,
};

export default AuthProvider;
