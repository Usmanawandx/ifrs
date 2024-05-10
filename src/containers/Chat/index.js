import React from 'react';
import { Container, NavBar } from 'components';
import PropTypes from 'prop-types';
import { getChats } from 'mocks/chats';
import ConversationList from './Conversation';

const chats = getChats();

const Chat = ({ navigation }) => (
  <Container backgroundColor="white">
    <NavBar
      title="Chat"
      onLeftIconPress={() => navigation.goBack()}
    />
    <ConversationList
      data={chats}
      onChatPress={(id) => navigation.navigate('ChatRoom', { id })}
    />
  </Container>
);

Chat.propTypes = {
  navigation: PropTypes.object.isRequired,
};

export default Chat;
